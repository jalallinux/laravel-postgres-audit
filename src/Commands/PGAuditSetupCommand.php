<?php

namespace JalalLinuX\PostgreAudit\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

class PGAuditSetupCommand extends Command
{
    private array $queries;

    public $signature = 'pg-audit:setup';

    public $description = 'Setup and configure Audit Log on database.';
    private ConnectionInterface $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = DB::connection($this->config('connection'));
        $this->queries = [
            file_get_contents(__DIR__ . '/../../database/queries/0_drop_table.sql.stub'),
            file_get_contents(__DIR__ . '/../../database/queries/1_create_table.sql.stub'),
            file_get_contents(__DIR__ . '/../../database/queries/2_jsonb_diff_val_function.sql.stub'),
            file_get_contents(__DIR__ . '/../../database/queries/3_logging_function.sql.stub'),
            file_get_contents(__DIR__ . '/../../database/queries/4_logging_triggers.sql.stub'),
        ];
    }

    public function handle(): int
    {
        if (config('database.connections.' . $this->config('connection'))['driver'] != 'pgsql') {
            $this->error("Connection '{$this->config('connection')}' driver must be pgsql.");
            return self::FAILURE;
        }

        if (!$this->confirm("Audit table name is '{$this->config('table_name')}' ?", true)) {
            return self::FAILURE;
        }

        [$primary_columns, $primary_columns_type, $target_tables, $except_tables, $except_users, $operations] = [
            $this->ask("Target tables primary columns (Like: id, uuid, slug, ...) ?", $this->config('primary_columns')),
            $this->ask("Target tables primary columns type (Like: bigint, uuid, varchar, ...) ?", $this->config('primary_columns_type')),
            $this->ask("Target tables (Split with comma) ?", $this->config('target_tables')),
            $this->ask("Except tables (Split with comma) ?", $this->config('except_tables')),
            $this->ask("Except users (Split with comma) ?", $this->config('except_users')),
            $this->ask("Target operations (Split with comma. Support: INSERT, DELETE, UPDATE) ?", $this->config('operations')),
        ];

        if (!$this->confirm("Are you ready to recreate audit table an overwrite trigger functions in database ?", true)) {
            $this->warn("- Audit setup canceled.");
            return self::FAILURE;
        }

        return $this->runQueries([
            'table_name' => $this->config('table_name'),
            'primary_columns' => $primary_columns,
            'primary_columns_type' => $primary_columns_type,
            'target_tables' => $this->reformatForWhereIn($target_tables == '*' ? implode(',', $this->tables()) : $target_tables),
            'except_tables' => "'{$this->config('table_name')}', {$this->reformatForWhereIn($except_tables)}",
            'except_users' => empty($except_users) ? 'NULL' : $this->reformatForWhereIn($except_users),
            'operations' => $this->reformatForWhereIn($operations),
        ]) ? self::SUCCESS : self::FAILURE;
    }


    private function reformatForWhereIn(string $text, string $separator = ',', string $around = "'"): string
    {
        return collect(explode($separator, $text))->map(fn($p) => $around . trim($p) . $around)->implode(', ');
    }

    private function runQueries(array $bindings = []): bool
    {
        $bindings = collect($bindings)->mapWithKeys(fn($v, $k) => ["<{$k}>" => $v]);
        $bar = $this->output->createProgressBar(count($this->queries) - 1);
        $bar->start();

        $this->db->beginTransaction();
        foreach ($this->queries as $query) {
            $query = str_replace($bindings->keys()->toArray(), $bindings->values()->toArray(), $query);
            try {
                $this->db->unprepared($this->db->raw($query));
                sleep(1);
                $bar->advance();
                continue;
            } catch (\Exception $exception) {
                $this->newLine(2);
                $this->warn($exception->getMessage());
                $this->error("Audit log setup failed.");
                $this->db->rollBack();
                return false;
            }
        }
        $this->db->commit();
        $bar->finish();
        $this->newLine(2);
        $this->info("Audit log setup completed.");
        return true;
    }

    private function config(string $key = null)
    {
        if (!is_null($key)) {
            $key = ".{$key}";
        }
        return config("pg_audit{$key}");
    }

    private function tables(): array
    {
        $tableSchema = $this->db->getConfig('schema') ?: 'public';
        return array_map('current', $this->db->select("SELECT table_name FROM information_schema.tables WHERE table_schema = '{$tableSchema}'"));
    }
}

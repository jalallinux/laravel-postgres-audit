<?php

namespace JalalLinuX\PostgreAudit;

use Illuminate\Support\ServiceProvider;

class PGAuditServiceProviders extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/postgres-audit.php', 'postgres-audit');
    }

    public function boot()
    {
        $this->registerCommands();
        $this->registerPublishing();
    }

    /**
     * Register Octane's publishing.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/postgres-audit.php' => config_path('postgres-audit.php'),
            ], 'postgres-audit-config');
        }
    }

    /**
     * Register the commands offered by Octane.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\PGAuditSetupCommand::class,
            ]);
        }
    }
}

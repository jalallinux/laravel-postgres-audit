<?php

namespace JalalLinuX\PostgreAudit;

use Illuminate\Support\ServiceProvider;

class PGAuditServiceProviders extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/postgre-audit.php', 'postgre-audit');
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
                __DIR__.'/../config/postgre-audit.php' => config_path('postgre-audit.php'),
            ], 'postgre-audit-config');
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

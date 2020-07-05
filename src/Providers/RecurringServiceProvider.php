<?php

namespace BlessingDube\Recurring\Providers;

use Illuminate\Support\ServiceProvider;

class RecurringServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateRecurringTable')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([__DIR__.'/../../database/migrations/create_recurrings_table.php' => database_path('migrations/'.$timestamp.'_create_recurring_table.php')],
                    'migrations');
            }

            $this->publishes([__DIR__.'/../../config/laravel-recurring.php' => config_path('laravel-recurring.php')],
                'config');

            $this->loadMigrationsFrom(__DIR__ .
                '/../../database/migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        //
    }
}

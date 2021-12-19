<?php

namespace OSSTools\Recurring\Providers;

use Illuminate\Support\ServiceProvider;

class RecurringServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateRecurringsTable')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([__DIR__.'/../../database/migrations/create_recurrings_table.php' => database_path('migrations/'.$timestamp.'_create_recurrings_table.php')],
                    'migrations');
            }
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

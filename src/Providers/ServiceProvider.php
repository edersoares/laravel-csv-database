<?php

namespace EderSoares\Laravel\CsvDatabase\Providers;

use EderSoares\Laravel\CsvDatabase\Console\Commands\CsvDatabaseImportCommand;
use EderSoares\Laravel\CsvDatabase\Console\Commands\CsvDatabaseImportFromCommand;
use EderSoares\Laravel\CsvDatabase\Console\Commands\CsvDatabaseMigrationCommand;
use EderSoares\Laravel\CsvDatabase\Console\Commands\CsvDatabaseMigrationFromCommand;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CsvDatabaseImportCommand::class,
                CsvDatabaseImportFromCommand::class,
                CsvDatabaseMigrationCommand::class,
                CsvDatabaseMigrationFromCommand::class,
            ]);
        }

        $this->mergeConfigFrom(__DIR__ . '/../../config/csv-database.php', 'csv-database');
    }
}

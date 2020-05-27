<?php

namespace EderSoares\Laravel\CsvDatabase\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CsvDatabaseImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:database:import {filename} {table} {--delimiter=,}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV file to database';

    /**
     * @param string $filename
     *
     * @return string
     */
    private function columns($filename)
    {
        $handle = fopen($filename, 'r');

        if (empty($handle)) {
            return '';
        }

        $columns = fgetcsv($handle, 0, config('csv-database.delimiter'));

        if (in_array('id', $columns)) {
            $columns = array_flip($columns);

            unset($columns['id']);

            $columns = array_flip($columns);

            array_unshift($columns, 'id_original');
        }

        return implode(', ', $columns);
    }

    /**
     * @param string $filename
     * @param string $table
     *
     * @return string
     */
    private function sql($filename, $table)
    {
        $columns = $this->columns($filename);
        $delimiter = config('csv-database.delimiter');

        return <<<SQL
COPY {$table}({$columns}) 
FROM '{$filename}' DELIMITER '{$delimiter}' CSV HEADER;
SQL;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filename = $this->argument('filename');
        $table = $this->argument('table');

        $sql = $this->sql($filename, $table);

        DB::unprepared($sql);

        $this->info('Imported table: ' . $table);
    }
}

<?php

namespace EderSoares\Laravel\CsvDatabase\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SplFileInfo;

class CsvDatabaseImportFromCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:database:import-from {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV files to database from path';

    /**
     * Execute the console command.
     *
     * @param Filesystem $filesystem
     *
     * @return mixed
     */
    public function handle(Filesystem $filesystem)
    {
        $files = $filesystem->allFiles($this->argument('path'));

        collect($files)->each(function (SplFileInfo $file) {
            if ($file->getExtension() === 'csv') {
                $this->call('csv:database:import', [
                    'filename' => $file->getPathname(),
                    'table' => 'migration.' . $file->getBasename('.csv'),
                ]);
            }
        });
    }
}

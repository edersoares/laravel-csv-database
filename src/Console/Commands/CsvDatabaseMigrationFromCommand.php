<?php

namespace EderSoares\Laravel\CsvDatabase\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SplFileInfo;

class CsvDatabaseMigrationFromCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:database:migrations-from {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create migration tables from path with CSV files';

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
                $this->call('csv:database:migration', [
                    'filename' => $file->getPathname(),
                    'table' => $file->getBasename('.csv'),
                ]);
            }
        });
    }
}

<?php

namespace EderSoares\Laravel\CsvDatabase\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CsvDatabaseMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:database:migration {filename} {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration table from CSV file';

    private function stub($replaces)
    {
        $stub = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClassName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('/* table */', function (Blueprint \$table) {
            /* columns */
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('/* table */');
    }
}

PHP;

        return str_replace(
            array_keys($replaces),
            array_values($replaces),
            $stub
        );
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function getMigrationColumns($filename)
    {
        $handle = fopen($filename, 'r');

        if (empty($handle)) {
            return '';
        }

        $header = fgetcsv($handle, 0, config('csv-database.delimiter'));
        $columns = [
            "\$table->id();",
        ];

        foreach ($header as $column) {
            if ($column === 'id') {
                $column = 'id_original';
            }

            $columns[] = "            \$table->text('{$column}')->nullable();";
        }

        $columns[] = "            \$table->timestamps();";

        fclose($handle);

        return implode("\n", $columns);
    }

    /**
     * @param string $table
     *
     * @return string
     */
    private function getMigrationClassName($table)
    {
        return 'Create' . Str::studly($table) . 'Table';
    }

    /**
     * @param string $table
     *
     * @return string
     */
    private function getFilename($table)
    {
        return database_path('migrations/' . date('Y_m_d_His_') . Str::snake($this->getMigrationClassName($table)) . '.php');
    }

    /**
     * Execute the console command.
     *
     * @param Filesystem $filesystem
     *
     * @return mixed
     */
    public function handle(Filesystem $filesystem)
    {
        $filename = $this->argument('filename');
        $table = $this->argument('table');

        $stub = $this->stub([
            'ClassName' => $this->getMigrationClassName($table),
            '/* table */' => $table,
            '/* columns */' => $this->getMigrationColumns($filename),
        ]);

        $filesystem->put($this->getFilename($table), $stub);

        $this->info('Migration created to table: ' . $table);
    }
}

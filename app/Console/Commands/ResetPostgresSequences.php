<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetPostgresSequences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-sequences';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all PostgreSQL sequences to match the current max ID in each table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            $this->error('This command only works with PostgreSQL databases.');
            return 1;
        }

        $this->info('Resetting PostgreSQL sequences...');

        // Obtener todas las tablas
        $tables = DB::select("
            SELECT tablename 
            FROM pg_tables 
            WHERE schemaname = 'public'
        ");

        $resetCount = 0;

        foreach ($tables as $table) {
            $tableName = $table->tablename;

            // Obtener las columnas de la tabla
            $columns = DB::select("
                SELECT column_name, column_default
                FROM information_schema.columns
                WHERE table_name = ? 
                AND column_default LIKE 'nextval%'
            ", [$tableName]);

            foreach ($columns as $column) {
                $columnName = $column->column_name;

                // Extraer el nombre de la secuencia del default
                preg_match("/nextval\('([^']+)'/", $column->column_default, $matches);
                
                if (isset($matches[1])) {
                    $sequenceName = $matches[1];

                    try {
                        // Obtener el valor máximo actual de la columna
                        $maxId = DB::table($tableName)->max($columnName);

                        if ($maxId !== null) {
                            // Resetear la secuencia al siguiente valor después del máximo
                            DB::statement("SELECT setval('{$sequenceName}', ?, false)", [$maxId + 1]);
                            
                            $this->line("✓ {$tableName}.{$columnName} → sequence reset to " . ($maxId + 1));
                            $resetCount++;
                        }
                    } catch (\Exception $e) {
                        $this->warn("⚠ Failed to reset {$tableName}.{$columnName}: " . $e->getMessage());
                    }
                }
            }
        }

        $this->newLine();
        $this->info("✓ Successfully reset {$resetCount} sequences!");

        return 0;
    }
}

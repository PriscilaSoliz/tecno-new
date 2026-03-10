<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cambiamos la columna fecha a timestamp para que acepte formato completo de fecha y hora
        // Usamos una sentencia raw para asegurar la conversión en PostgreSQL
        DB::statement('ALTER TABLE pagos ALTER COLUMN fecha TYPE TIMESTAMP WITHOUT TIME ZONE USING fecha::timestamp without time zone');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE pagos ALTER COLUMN fecha TYPE VARCHAR(10)');
    }
};

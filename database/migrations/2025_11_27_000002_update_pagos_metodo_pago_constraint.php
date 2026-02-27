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
        // Eliminar la restricción existente
        DB::statement("ALTER TABLE pagos DROP CONSTRAINT IF EXISTS pagos_metodo_pago_check");
        // Crear nueva restricción que incluya PAGO_FACIL
        DB::statement("ALTER TABLE pagos ADD CONSTRAINT pagos_metodo_pago_check CHECK (metodo_pago IN ('EFECTIVO', 'TARJETA', 'PAGO_FACIL'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los cambios
        DB::statement("ALTER TABLE pagos DROP CONSTRAINT pagos_metodo_pago_check");
        DB::statement("ALTER TABLE pagos ADD CONSTRAINT pagos_metodo_pago_check CHECK (metodo_pago IN ('EFECTIVO', 'TARJETA'))");
    }
};

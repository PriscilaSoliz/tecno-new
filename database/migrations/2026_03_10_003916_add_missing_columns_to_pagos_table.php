<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega las columnas que el controlador PagoFacilController necesita
     * pero que no existían en la tabla pagos original.
     */
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Estado del pago: pendiente, completado, fallido, etc.
            $table->string('estado')->default('pendiente')->after('metodo_pago');

            // Referencia externa (ej: "venta-18-1773103042")
            $table->string('referencia_externa')->nullable()->after('estado');

            // ID de transacción devuelto por PagoFácil
            $table->string('transaction_id')->nullable()->after('referencia_externa');

            // Fecha real de pago (cuando se confirma)
            $table->timestamp('fecha_pago')->nullable()->after('transaction_id');

            // JSON con datos completos de la respuesta de PagoFácil + cuota_id
            $table->json('datos_pago')->nullable()->after('fecha_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['estado', 'referencia_externa', 'transaction_id', 'fecha_pago', 'datos_pago']);
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // Agregar columnas si no existen
            if (!Schema::hasColumn('pagos', 'referencia_externa')) {
                $table->string('referencia_externa')->nullable()->after('metodo_pago');
            }
            if (!Schema::hasColumn('pagos', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('referencia_externa');
            }
            if (!Schema::hasColumn('pagos', 'estado')) {
                $table->enum('estado', ['pendiente', 'completado', 'rechazado'])->default('pendiente')->after('transaction_id');
            }
            if (!Schema::hasColumn('pagos', 'fecha_pago')) {
                $table->timestamp('fecha_pago')->nullable()->after('estado');
            }
            if (!Schema::hasColumn('pagos', 'datos_pago')) {
                $table->json('datos_pago')->nullable()->after('fecha_pago');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn([
                'referencia_externa',
                'transaction_id',
                'estado',
                'fecha_pago',
                'datos_pago'
            ]);
        });
    }
};

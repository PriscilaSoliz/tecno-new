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
            // Campos específicos para Pago Fácil (solo si no existen)
            if (!Schema::hasColumn('pagos', 'referencia_externa')) {
                $table->string('referencia_externa')->nullable()->after('metodo_pago');
            }
            if (!Schema::hasColumn('pagos', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('referencia_externa');
            }
            if (!Schema::hasColumn('pagos', 'datos_pago')) {
                $table->json('datos_pago')->nullable()->after('transaction_id');
            }
            if (!Schema::hasColumn('pagos', 'fecha_pago')) {
                $table->timestamp('fecha_pago')->nullable()->after('datos_pago');
            }
            if (!Schema::hasColumn('pagos', 'estado')) {
                $table->string('estado')->default('pendiente')->after('fecha_pago');
            }
            
            // Índices para mejorar el rendimiento (solo si no existen)
            if (!Schema::hasIndex('pagos', 'pagos_referencia_externa_index')) {
                $table->index(['referencia_externa']);
            }
            if (!Schema::hasIndex('pagos', 'pagos_transaction_id_index')) {
                $table->index(['transaction_id']);
            }
            if (!Schema::hasIndex('pagos', 'pagos_estado_index')) {
                $table->index(['estado']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex(['referencia_externa']);
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['estado']);
            
            $table->dropColumn([
                'referencia_externa',
                'transaction_id',
                'datos_pago',
                'fecha_pago',
                'estado'
            ]);
        });
    }
};

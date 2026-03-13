<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Agrega la columna 'stock' a la tabla productos para gestionar
     * el inventario disponible en el catálogo de venta.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            // Stock disponible para venta en catálogo (unidades)
            // Se incrementa al finalizar órdenes de producción
            // Se decrementa al confirmar ventas
            $table->integer('stock')->default(0)->after('has_promo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};

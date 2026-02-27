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
        Schema::create('lote_insumos', function (Blueprint $table) {
            $table->id();   
            $table->date('fecha_ingreso');
            $table->decimal('cantidad_total_x_unidad', 10, 2);
            $table->decimal('cantidad_disponible_x_unidad', 10, 2);
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('costo_lote', 10, 2);
            
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->foreign('proveedor_id')->references('id')->on('proveedors');

            $table->unsignedBigInteger('ingrediente_id');
            $table->foreign('ingrediente_id')->references('id')->on('ingredientes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_insumos');
    }
};

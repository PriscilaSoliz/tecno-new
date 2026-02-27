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
        Schema::create('consumo_insumos', function (Blueprint $table) {
            $table->id();
            $table->decimal('cantidad_consumida', 10, 2);
            $table->unsignedBigInteger('orden_produccion_id');
            $table->foreign('orden_produccion_id')->references('id')->on('orden_produccions');
            $table->unsignedBigInteger('ingrediente_id');
            $table->foreign('ingrediente_id')->references('id')->on('ingredientes');
            $table->unsignedBigInteger('lote_insumo_id')->nullable(); // Puede ser nulo si el consumo no está ligado a un lote específico
            $table->foreign('lote_insumo_id')->references('id')->on('lote_insumos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumo_insumos');
    }
};

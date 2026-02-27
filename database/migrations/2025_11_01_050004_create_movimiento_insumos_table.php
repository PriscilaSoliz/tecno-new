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
        Schema::create('movimiento_insumos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->decimal('cantidad', 10, 2);
            $table->string('tipo_movimiento')->default('entrada');
            $table->string('motivo_movimiento')->nullable();
            $table->unsignedBigInteger('ingrediente_id');
            $table->foreign('ingrediente_id')->references('id')->on('ingredientes');
            $table->unsignedBigInteger('lote_insumo_id')->nullable(); // Puede ser nulo si es una salida de stock general
            $table->foreign('lote_insumo_id')->references('id')->on('lote_insumos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_insumos');
    }
};

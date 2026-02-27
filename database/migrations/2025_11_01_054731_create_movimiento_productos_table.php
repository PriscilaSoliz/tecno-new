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
        Schema::create('movimiento_productos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->integer('cantidad');
            $table->string('tipo_movimiento'); // 'entrada' o 'salida'
            $table->string('motivo_movimiento')->nullable();
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->unsignedBigInteger('producto_terminado_id')->nullable(); // Puede ser nulo si es una salida de stock general
            $table->foreign('producto_terminado_id')->references('id')->on('producto_terminados');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_productos');
    }
};

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
        Schema::create('producto_terminados', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_produccion');
            $table->integer('cantidad_producida');
            $table->unsignedBigInteger('orden_produccion_id');
            $table->foreign('orden_produccion_id')->references('id')->on('orden_produccions');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_terminados');
    }
};

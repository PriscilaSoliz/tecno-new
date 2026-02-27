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
        Schema::create('orden_produccions', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_creacion');
            $table->integer('cantidad_a_producir');
            $table->string('estado')->default('pendiente'); // Ej: pendiente, en_proceso, completada, cancelada
            $table->unsignedBigInteger('operario_id')->nullable();
            $table->foreign('operario_id')->references('id')->on('operarios');
            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_produccions');
    }
};

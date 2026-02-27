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
        Schema::create('cuenta_cobros', function (Blueprint $table) {
            $table->id();
            $table->decimal('saldo_pendiente', 10, 2);
            $table->date('fecha_vencimiento');

            $table->unsignedBigInteger('venta_id');
            $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');

            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobros');
    }
};

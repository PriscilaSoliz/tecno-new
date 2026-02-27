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
        Schema::create('pedido_rastreos', function (Blueprint $table) {
            $table->id();
          
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->timestamp('hora');
            
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_rastreos');
    }
};

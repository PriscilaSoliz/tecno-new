<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('page_name'); // nombre de la página (ej: 'productos.index', 'almacen.index')
            $table->string('page_title'); // título legible (ej: 'Lista de Productos')
            $table->string('page_path'); // ruta (ej: '/produccion/productos')
            $table->integer('visit_count')->default(1); // contador de visitas
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // usuario (opcional)
            $table->timestamps();
            
            // índices para búsquedas rápidas
            $table->unique(['page_name', 'user_id']);
            $table->index('page_name');
            $table->index('user_id');
        });
    }

    public function rollback(): void
    {
        Schema::dropIfExists('page_visits');
    }
};

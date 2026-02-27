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
        // Eliminamos la tabla si existe para asegurar la estructura correcta
        Schema::dropIfExists('page_visits');

        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('page_name')->nullable()->index(); // Nombre de la ruta (ej: dashboard)
            $table->string('page_title')->nullable(); // Título legible
            $table->string('page_path')->index(); // URL (ej: /dashboard)
            $table->unsignedBigInteger('visit_count')->default(0);
            $table->unsignedBigInteger('user_id')->nullable()->index(); // Para seguimiento por usuario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_visits');
    }
};

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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('route')->nullable(); // nombre de ruta de Laravel o URL
            $table->string('icon')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable(); // para submenus
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->json('roles')->nullable(); // opcional: ["admin","user"]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};

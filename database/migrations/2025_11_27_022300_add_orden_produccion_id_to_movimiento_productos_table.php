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
        Schema::table('movimiento_productos', function (Blueprint $table) {
            $table->unsignedBigInteger('orden_produccion_id')->nullable()->after('producto_id');
            $table->foreign('orden_produccion_id')->references('id')->on('orden_produccions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimiento_productos', function (Blueprint $table) {
            $table->dropForeign(['orden_produccion_id']);
            $table->dropColumn('orden_produccion_id');
        });
    }
};

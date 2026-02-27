<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modificar tabla promocions
        Schema::table('promocions', function (Blueprint $table) {
            // Eliminar columnas que ya no se usarán
            // Usamos if exists por seguridad si se corre en un entorno donde ya se borraron
            if (Schema::hasColumn('promocions', 'tipo'))
                $table->dropColumn('tipo');
            if (Schema::hasColumn('promocions', 'valor'))
                $table->dropColumn('valor');
            if (Schema::hasColumn('promocions', 'nombre'))
                $table->dropColumn('nombre');

            // Agregar nuevas columnas
            if (!Schema::hasColumn('promocions', 'producto_id')) {
                $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            }
            if (!Schema::hasColumn('promocions', 'precio')) {
                $table->decimal('precio', 10, 2);
            }
        });

        // Modificar tabla productos
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'has_promo')) {
                $table->boolean('has_promo')->default(false)->after('precio_venta');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promocions', function (Blueprint $table) {
            // Revertir cambios (es complicado revertir datos perdidos, solo estructura)
            $table->string('nombre')->nullable();
            $table->string('tipo')->nullable();
            $table->decimal('valor', 8, 2)->nullable();

            $table->dropForeign(['producto_id']);
            $table->dropColumn('producto_id');
            $table->dropColumn('precio');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('has_promo');
        });
    }
};

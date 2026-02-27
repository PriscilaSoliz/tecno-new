<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoTerminadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\ProductoTerminado::create([
            'fecha_produccion' => now(),
            'cantidad_producida' => 95,
            'orden_produccion_id' => 1,
            'producto_id' => 1, // Pan de Molde Blanco
        ]);

        \App\Models\ProductoTerminado::create([
            'fecha_produccion' => now()->addDays(1),
            'cantidad_producida' => 48,
            'orden_produccion_id' => 2,
            'producto_id' => 2, // Croissant de Mantequilla
        ]);

        \App\Models\ProductoTerminado::create([
            'fecha_produccion' => now()->addDays(2),
            'cantidad_producida' => 70,
            'orden_produccion_id' => 3,
            'producto_id' => 3, // Pan Integral con Semillas
        ]);

    }
}

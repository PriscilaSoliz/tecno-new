<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdenProduccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\OrdenProduccion::create([
            'fecha_creacion' => now(),
            'cantidad_a_producir' => 100,
            'estado' => 'pendiente',
            'producto_id' => 1, // Pan de Molde Blanco
        ]);

        \App\Models\OrdenProduccion::create([
            'fecha_creacion' => now()->addDays(1),
            'cantidad_a_producir' => 50,
            'estado' => 'en_proceso',
            'producto_id' => 2, // Croissant de Mantequilla
        ]);

        \App\Models\OrdenProduccion::create([
            'fecha_creacion' => now()->addDays(2),
            'cantidad_a_producir' => 75,
            'estado' => 'completada',
            'producto_id' => 3, // Pan Integral con Semillas
        ]);
    }
}

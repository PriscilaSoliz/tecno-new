<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromocionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Promocion::create([
            'nombre' => 'Descuento de Verano',
            'tipo' => 'porcentaje',
            'valor' => 15.00,
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(30),
            'is_active' => true,
        ]);

        \App\Models\Promocion::create([
            'nombre' => '2x1 en Croissants',
            'tipo' => 'cantidad',
            'valor' => 1.00, // Representa "lleva 2, paga 1"
            'fecha_inicio' => now()->addDays(5),
            'fecha_fin' => now()->addDays(15),
            'is_active' => true,
        ]);

        \App\Models\Promocion::create([
            'nombre' => 'Envío Gratis',
            'tipo' => 'envio',
            'valor' => 0.00,
            'fecha_inicio' => now()->subDays(10),
            'fecha_fin' => now()->addDays(10),
            'is_active' => false, // Promoción inactiva
        ]);

    }
}

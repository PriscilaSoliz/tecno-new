<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConsumoInsumoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\ConsumoInsumo::create([
            'cantidad_consumida' => 5.00,
            'orden_produccion_id' => 1,
            'ingrediente_id' => 1, // Harina de Trigo
            'lote_insumo_id' => 1, // Lote de Harina 1
        ]);

        \App\Models\ConsumoInsumo::create([
            'cantidad_consumida' => 1.00,
            'orden_produccion_id' => 1,
            'ingrediente_id' => 2, // Azúcar Blanca
            'lote_insumo_id' => 2, // Lote de Azúcar 1
        ]);

        \App\Models\ConsumoInsumo::create([
            'cantidad_consumida' => 0.50,
            'orden_produccion_id' => 2,
            'ingrediente_id' => 1, // Harina de Trigo
            'lote_insumo_id' => 1, // Lote de Harina 1
        ]);

        \App\Models\ConsumoInsumo::create([
            'cantidad_consumida' => 0.10,
            'orden_produccion_id' => 2,
            'ingrediente_id' => 3, // Levadura Fresca
            'lote_insumo_id' => 3, // Lote de Levadura 1
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Receta para Pan Casero (producto_base_id = 1)
        \App\Models\Receta::insert([
             [
                'producto_id' => 1,
                'ingrediente_id' => 1, // Harina
                'cant_x_unidad' => 0.01,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'producto_id' => 1,
                'ingrediente_id' => 2, // Leche
                'cant_x_unidad' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'producto_id' => 1,
                'ingrediente_id' => 4, // Manteca
                'cant_x_unidad' => 0.2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Receta para Pan Francés (producto_base_id = 2)
        \App\Models\Receta::insert([
             [
                'producto_id' => 2,
                'ingrediente_id' => 1, // Harina
                'cant_x_unidad' => 0.01,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'producto_id' => 2,
                'ingrediente_id' => 2, // Leche
                'cant_x_unidad' => 0.5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Receta para Pan Integral (producto_base_id = 3)
        \App\Models\Receta::insert([
             [
                'producto_id' => 3,
                'ingrediente_id' => 1, // Harina
                'cant_x_unidad' => 0.01,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

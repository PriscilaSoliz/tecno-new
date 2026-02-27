<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ingredientes')->insert([
            // Ingredientes para panadería
            [
                'nombre' => 'Harina',
                'unidad_medida' => 'Quintal',
                'descripcion' => 'Harina de trigo para panadería',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Leche',
                'unidad_medida' => 'Kg',
                'descripcion' => 'Leche entera fresca',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Queso',
                'unidad_medida' => 'Kg',
                'descripcion' => 'Queso fresco para rellenos',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Manteca',
                'unidad_medida' => 'Kg',
                'descripcion' => 'Manteca vegetal para repostería',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Azúcar',
                'unidad_medida' => 'Kg',
                'descripcion' => 'Azúcar blanca granulada',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Sal',
                'unidad_medida' => 'Kg',
                'descripcion' => 'Sal de mesa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Levadura',
                'unidad_medida' => 'Kg',
                'descripcion' => 'Levadura fresca para pan',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

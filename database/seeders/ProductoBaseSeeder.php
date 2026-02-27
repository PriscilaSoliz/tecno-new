<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoBaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('producto_bases')->insert([
            [
                'nombre' => 'Pan Casero',
                'precio' => 15.00,
                'descripcion' => 'Pan horneado artesanalmente.',
            ],
            [
                'nombre' => 'Pan Francés',
                'precio' => 12.00,
                'descripcion' => 'Baguette crujiente y dorada.',
            ],
            [
                'nombre' => 'Pan Integral',
                'precio' => 18.00,
                'descripcion' => 'Pan saludable con harina integral y semillas.',
            ],
        ]);
    }
}
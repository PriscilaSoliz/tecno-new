<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('proveedors')->insert([
            [
                'empresa' => 'Proveedor de Harinas S.A.',
                'contacto' => 'Juan Pérez',
                'estado' => true,
            ],
            [
                'empresa' => 'Lácteos del Valle',
                'contacto' => 'María García',
                'estado' => true,
            ],
            [
                'empresa' => 'Distribuidora de Quesos "El Buen Gusto"',
                'contacto' => 'Carlos Ruíz',
                'estado' => true,
            ],
            [
                'empresa' => 'Grasas y Aceites "La Cocina"',
                'contacto' => 'Ana López',
                'estado' => true,
            ],
        ]);
    }
}

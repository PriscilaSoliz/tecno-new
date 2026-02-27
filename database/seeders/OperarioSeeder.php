<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Operario::create([
            'turno' => 'Mañana',
            'especialidad' => 'Panadero',
            'user_id' => 7, // Asigna al 'Operario Uno' (produccion)
        ]);

        \App\Models\Operario::create([
            'turno' => 'Tarde',
            'especialidad' => 'Pastelero',
            'user_id' => 8, // Asigna al 'Operario Dos' (produccion)
        ]);

    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CuentaCobroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\CuentaCobro::create([
            'saldo_pendiente' => 100.00,
            'fecha_vencimiento' => now()->addDays(30),
            'venta_id' => 1,
            'cliente_id' => 1,
        ]);

        \App\Models\CuentaCobro::create([
            'saldo_pendiente' => 250.00,
            'fecha_vencimiento' => now()->addDays(45),
            'venta_id' => 2,
            'cliente_id' => 2,
        ]);
    }
}

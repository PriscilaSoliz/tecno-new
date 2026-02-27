<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PagosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Pagos::create([
            'fecha' => now(),
            'monto' => 45.00,
            'metodo_pago' => 'efectivo',
            'venta_id' => 1,
        ]);

        \App\Models\Pagos::create([
            'fecha' => now()->addDays(1),
            'monto' => 30.00,
            'metodo_pago' => 'tarjeta',
            'venta_id' => 2,
        ]);

        \App\Models\Pagos::create([
            'fecha' => now()->addDays(2),
            'monto' => 60.00,
            'metodo_pago' => 'efectivo',
            'venta_id' => 3,
        ]);

    }
}

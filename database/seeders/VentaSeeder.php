<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Venta::create([
            'fecha' => now(),
            'total' => 45.00,
            'tipo_pago' => 'efectivo',
            'pedido_id' => 1,
            'cliente_id' => 1,
        ]);

        \App\Models\Venta::create([
            'fecha' => now()->addDays(1),
            'total' => 30.00,
            'tipo_pago' => 'tarjeta',
            'pedido_id' => 2,
            'cliente_id' => 2,
        ]);

        \App\Models\Venta::create([
            'fecha' => now()->addDays(2),
            'total' => 60.00,
            'tipo_pago' => 'efectivo',
            'pedido_id' => 3,
            'cliente_id' => 1,
        ]);
        
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetalleVentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\DetalleVenta::create([
            'cantidad' => 2,
            'precio_unitario' => 50.00,
            'subtotal' => 100.00,
            'venta_id' => 1,
            'producto_id' => 1,
        ]);

        \App\Models\DetalleVenta::create([
            'cantidad' => 1,
            'precio_unitario' => 75.00,
            'subtotal' => 75.00,
            'venta_id' => 1,
            'producto_id' => 2,
        ]);

        \App\Models\DetalleVenta::create([
            'cantidad' => 3,
            'precio_unitario' => 20.00,
            'subtotal' => 60.00,
            'venta_id' => 2,
            'producto_id' => 3,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PedidoDetalleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\PedidoDetalle::create([
            'cantidad' => 2,
            'precio_unitario' => 25.00,
            'subtotal' => 50.00,
            'pedido_id' => 1,
            'producto_id' => 1, // Pan de Molde Blanco
        ]);

        \App\Models\PedidoDetalle::create([
            'cantidad' => 1,
            'precio_unitario' => 15.00,
            'subtotal' => 15.00,
            'pedido_id' => 1,
            'producto_id' => 2, // Croissant de Mantequilla
        ]);

        \App\Models\PedidoDetalle::create([
            'cantidad' => 3,
            'precio_unitario' => 15.00,
            'subtotal' => 45.00,
            'pedido_id' => 2,
            'producto_id' => 2, // Croissant de Mantequilla
        ]);

        \App\Models\PedidoDetalle::create([
            'cantidad' => 2,
            'precio_unitario' => 30.00,
            'subtotal' => 60.00,
            'pedido_id' => 3,
            'producto_id' => 3, // Pan Integral con Semillas
        ]);

    }
}

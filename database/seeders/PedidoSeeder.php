<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Pedido::create([
            'fecha' => now(),
            'estado_produccion' => 'pendiente',
            'total' => 45.00,
            'cliente_id' => 1, // Cliente Uno
        ]);

        \App\Models\Pedido::create([
            'fecha' => now()->addDays(1),
            'estado_produccion' => 'en_proceso',
            'total' => 30.00,
            'cliente_id' => 1, // Cliente Dos
        ]);

        \App\Models\Pedido::create([
            'fecha' => now()->addDays(2),
            'estado_produccion' => 'completado',
            'total' => 60.00,
            'cliente_id' => 1, // Cliente Uno
        ]);

    }
}

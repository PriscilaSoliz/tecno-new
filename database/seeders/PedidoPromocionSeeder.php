<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PedidoPromocionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\PedidoPromocion::create([
            'pedido_id' => 1,
            'promocion_id' => 1,
        ]);

        \App\Models\PedidoPromocion::create([
            'pedido_id' => 2,
            'promocion_id' => 2,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MovimientoProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\MovimientoProducto::create([
            'fecha' => now(),
            'cantidad' => 95,
            'tipo_movimiento' => 'entrada',
            'motivo_movimiento' => 'Producción de Pan de Molde Blanco',
            'producto_id' => 1, // Pan de Molde Blanco
            'producto_terminado_id' => 1, // Producto Terminado 1
        ]);

        \App\Models\MovimientoProducto::create([
            'fecha' => now()->addDays(1),
            'cantidad' => 48,
            'tipo_movimiento' => 'entrada',
            'motivo_movimiento' => 'Producción de Croissant de Mantequilla',
            'producto_id' => 2, // Croissant de Mantequilla
            'producto_terminado_id' => 2, // Producto Terminado 2
        ]);

        \App\Models\MovimientoProducto::create([
            'fecha' => now()->addDays(2),
            'cantidad' => 70,
            'tipo_movimiento' => 'entrada',
            'motivo_movimiento' => 'Producción de Pan Integral con Semillas',
            'producto_id' => 3, // Pan Integral con Semillas
            'producto_terminado_id' => 3, // Producto Terminado 3
        ]);

        \App\Models\MovimientoProducto::create([
            'fecha' => now()->addDays(3),
            'cantidad' => 10,
            'tipo_movimiento' => 'salida',
            'motivo_movimiento' => 'Venta a cliente',
            'producto_id' => 1, // Pan de Molde Blanco
            'producto_terminado_id' => null, // Salida de stock general
        ]);

        \App\Models\MovimientoProducto::create([
            'fecha' => now()->addDays(3),
            'cantidad' => 5,
            'tipo_movimiento' => 'salida',
            'motivo_movimiento' => 'Venta a cliente', 
            'producto_id' => 2, // Croissant de Mantequilla 
            'producto_terminado_id' => null, // Salida de stock general 
        ]);
    }
}

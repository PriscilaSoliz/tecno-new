<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InsumoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear ingredientes
        $harina = \App\Models\Ingrediente::create([
            'nombre' => 'Harina de Trigo',
            'unidad_medida' => 'kg',
            'descripcion' => 'Harina de trigo para panadería',
            'is_active' => true,
        ]);

        $azucar = \App\Models\Ingrediente::create([
            'nombre' => 'Azúcar Blanca',
            'unidad_medida' => 'kg',
            'descripcion' => 'Azúcar refinada para repostería',
            'is_active' => true,
        ]);

        $levadura = \App\Models\Ingrediente::create([
            'nombre' => 'Levadura Fresca',
            'unidad_medida' => 'gr',
            'descripcion' => 'Levadura para panificación',
            'is_active' => true,
        ]);

        // Crear lotes de insumos
        $loteHarina1 = \App\Models\LoteInsumo::create([
            'fecha_ingreso' => now(),
            'cantidad_total_x_unidad' => 50.00,
            'cantidad_disponible_x_unidad' => 50.00,
            'costo_unitario' => 1.50,
            'costo_lote' => 75.00, // 50 * 1.50
            'ingrediente_id' => $harina->id,
        ]);

        $loteAzucar1 = \App\Models\LoteInsumo::create([
            'fecha_ingreso' => now(),
            'cantidad_total_x_unidad' => 25.00,
            'cantidad_disponible_x_unidad' => 25.00,
            'costo_unitario' => 0.80,
            'costo_lote' => 20.00, // 25 * 0.80
            'ingrediente_id' => $azucar->id,
        ]);

        $loteLevadura1 = \App\Models\LoteInsumo::create([
            'fecha_ingreso' => now(),
            'cantidad_total_x_unidad' => 1000.00, // 1 kg
            'cantidad_disponible_x_unidad' => 1000.00,
            'costo_unitario' => 0.50,
            'costo_lote' => 500.00, // 1000 * 0.50
            'ingrediente_id' => $levadura->id,
        ]);
        // Crear movimientos de insumos (ejemplos)
        \App\Models\MovimientoInsumo::create([
            'fecha' => now(),
            'cantidad' => 10.00,
            'tipo_movimiento' => 'entrada',
            'motivo_movimiento' => 'Compra a proveedor',
            'ingrediente_id' => $harina->id,
            'lote_insumo_id' => $loteHarina1->id,
        ]);

        \App\Models\MovimientoInsumo::create([
            'fecha' => now(),
            'cantidad' => 2.00,
            'tipo_movimiento' => 'salida',
            'motivo_movimiento' => 'Uso en producción',
            'ingrediente_id' => $harina->id,
            'lote_insumo_id' => $loteHarina1->id,
        ]);

        \App\Models\MovimientoInsumo::create([
            'fecha' => now(),
            'cantidad' => 5.00,
            'tipo_movimiento' => 'entrada',
            'motivo_movimiento' => 'Compra a proveedor',
            'ingrediente_id' => $azucar->id,
            'lote_insumo_id' => $loteAzucar1->id,
        ]);
    }
}

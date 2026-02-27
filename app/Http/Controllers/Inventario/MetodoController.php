<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Ingrediente;
use App\Models\MovimientoInsumo;
use App\Models\LoteInsumo;
use Illuminate\Support\Facades\DB;

class MetodoController extends Controller
{
    public function index()
    {
        return Inertia::render('Contabilidads/Index');
    }

    // Helper para obtener datos base
    private function getKardexData(Request $request, $metodo)
    {
        $ingredientes = Ingrediente::select('id', 'nombre', 'unidad_medida')
            ->where('is_active', true)
            ->get();
        
        $kardex = [];
        $selectedIngrediente = null;
        $existenciaActual = 0;
        $valorInventario = 0;

        if ($request->filled('ingrediente_id')) {
            $selectedIngrediente = Ingrediente::find($request->ingrediente_id);
            
            // Obtenemos movimientos reales de la BD ordenados por fecha
            $movimientos = MovimientoInsumo::with(['loteInsumo'])
                ->where('ingrediente_id', $request->ingrediente_id)
                ->when($request->filled('fecha_inicio'), fn($q) => $q->whereDate('fecha', '>=', $request->fecha_inicio))
                ->when($request->filled('fecha_fin'), fn($q) => $q->whereDate('fecha', '<=', $request->fecha_fin))
                ->orderBy('fecha', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Calculamos el Kardex según el método
            $saldoCantidad = 0;
            $saldoTotal = 0;

            foreach ($movimientos as $mov) {
                // Obtener precio unitario del lote (costo_unitario en la migración)
                $precioUnitario = $mov->loteInsumo->costo_unitario ?? 0;
                $cantidad = $mov->cantidad;
                $totalMov = $cantidad * $precioUnitario;

                // Lógica específica para Promedio Ponderado (Recalcular costo)
                if ($metodo === 'PROMEDIO') {
                    if ($mov->tipo_movimiento === 'entrada') {
                        $saldoTotal += $totalMov;
                        $saldoCantidad += $cantidad;
                    } else {
                        // En salida promedio, usamos el costo promedio actual
                        $costoPromedio = $saldoCantidad > 0 ? ($saldoTotal / $saldoCantidad) : 0;
                        $precioUnitario = $costoPromedio; 
                        $totalMov = $cantidad * $precioUnitario;
                        
                        $saldoTotal -= $totalMov;
                        $saldoCantidad -= $cantidad;
                    }
                } else {
                    // PEPS y UEPS (Usamos el costo real del lote que ya se guardó en la BD)
                    if ($mov->tipo_movimiento === 'entrada') {
                        $saldoCantidad += $cantidad;
                        $saldoTotal += $totalMov;
                    } else {
                        $saldoCantidad -= $cantidad;
                        $saldoTotal -= $totalMov;
                    }
                }

                // Calcular existencias actuales (cantidad_disponible en lotes)
                $existenciaLote = $mov->loteInsumo->cantidad_disponible_x_unidad ?? 0;

                $kardex[] = [
                    'fecha' => $mov->fecha,
                    'tipo' => strtoupper($mov->tipo_movimiento), // Convertir a mayúsculas
                    'motivo' => $mov->motivo_movimiento ?? '-',
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'total' => $totalMov,
                    'saldo_cantidad' => max(0, $saldoCantidad),
                    'saldo_total' => max(0, $saldoTotal),
                    'existencia_lote' => $existenciaLote, // Disponible en el lote específico
                ];
            }

            // Totales finales
            $existenciaActual = $saldoCantidad;
            $valorInventario = $saldoTotal;
        }

        return [
            'ingredientes' => $ingredientes,
            'kardex' => $kardex,
            'selectedIngrediente' => $selectedIngrediente,
            'existenciaActual' => round($existenciaActual, 2),
            'valorInventario' => round($valorInventario, 2),
            'filters' => $request->all(['ingrediente_id', 'fecha_inicio', 'fecha_fin'])
        ];
    }

    public function indexPEPS(Request $request)
    {
        $data = $this->getKardexData($request, 'PEPS');
        return Inertia::render('Contabilidads/Peps/Index', $data);
    }

    public function indexUEPS(Request $request)
    {
        $data = $this->getKardexData($request, 'UEPS');
        return Inertia::render('Contabilidads/Ueps/Index', $data);
    }

    public function indexPromedioPonderado(Request $request)
    {
        $data = $this->getKardexData($request, 'PROMEDIO');
        return Inertia::render('Contabilidads/Promedio/Index', $data);
    }
}

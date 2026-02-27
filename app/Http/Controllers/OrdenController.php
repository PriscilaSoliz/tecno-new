<?php

namespace App\Http\Controllers;

use App\Models\OrdenProduccion;
use App\Models\Producto;
use App\Models\LoteInsumo;
use App\Models\ConsumoInsumo;
use App\Models\MovimientoInsumo;
use App\Models\ProductoTerminado;
use App\Models\MovimientoProducto;
use App\Models\Operario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // compatible con Controller::index() — Request opcional
    public function index(Request $request = null)
    {
        if (! $request) {
            $request = request();
        }
        $query = OrdenProduccion::with(['producto', 'operario']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('operario_id')) {
            $query->where('operario_id', $request->operario_id);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('producto', fn($a) => $a->where('nombre', 'like', "%{$q}%"))
                  ->orWhere('id', $q);
        }

        $ordenes = OrdenProduccion::with(['producto', 'operario'])
            ->orderBy('id', 'desc')
            ->get();

        // Cargar productos con recetas (cada receta es un ingrediente)
        $productos = Producto::with(['recetas.ingrediente'])->where('is_active', true)->get();

        // Para cada receta (ingrediente) añadir available_stock y lotes
        foreach ($productos as $p) {
            foreach ($p->recetas as $receta) {
                $ingredienteId = $receta->ingrediente_id;
                $available = LoteInsumo::where('ingrediente_id', $ingredienteId)
                    ->where('cantidad_disponible_x_unidad', '>', 0)
                    ->sum('cantidad_disponible_x_unidad');
                $receta->available_stock = $available;

                // Enviar lista de lotes para la simulación (PEPS asc)
                $lotes = LoteInsumo::where('ingrediente_id', $ingredienteId)
                    ->where('cantidad_disponible_x_unidad', '>', 0)
                    ->orderBy('fecha_ingreso', 'ASC')
                    ->get(['id', 'cantidad_disponible_x_unidad', 'fecha_ingreso', 'costo_unitario'])
                    ->toArray();
                $receta->lotes = $lotes;
            }
        }

        $operarios = Operario::select('id', 'turno', 'especialidad')->get();

        return Inertia::render('Produccion/Ordenes/Index', [
            'ordenes' => $ordenes,
            'productos' => $productos,
            'operarios' => $operarios,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * 🟢 MOMENTO 1: INTENCIÓN (Crear Orden)
     * Solo registra la planificación. NO toca inventario.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad_a_producir' => 'required|numeric|min:1',
            // no recibimos operario ni fecha (se usan server-side)
        ]);

        $producto = Producto::with(['recetas.ingrediente'])->findOrFail($data['producto_id']);

        if ($producto->recetas->isEmpty()) {
            return back()->withErrors(['producto_id' => 'Este producto no tiene receta configurada. No se puede producir.']);
        }

        DB::beginTransaction();
        try {
            // 1) Validar stock total por ingrediente (no modificar todavía)
            foreach ($producto->recetas as $receta) {
                $needed = $receta->cant_x_unidad * $data['cantidad_a_producir'];
                $available = LoteInsumo::where('ingrediente_id', $receta->ingrediente_id)
                    ->where('cantidad_disponible_x_unidad', '>', 0)
                    ->sum('cantidad_disponible_x_unidad');

                if ($available < $needed) {
                    throw new \Exception("Stock insuficiente para {$receta->ingrediente->nombre}. Necesitas {$needed}, disponible {$available}.");
                }
            }

            // 2) Crear orden (fecha now), asociar operario si el usuario tiene uno
            $operario_id = null;
            if (auth()->check() && auth()->user()->operario) {
                $operario_id = auth()->user()->operario->id;
            }

            $orden = OrdenProduccion::create([
                'producto_id' => $data['producto_id'],
                'cantidad_a_producir' => $data['cantidad_a_producir'],
                'operario_id' => $operario_id,
                'fecha_creacion' => now(),
                'estado' => 'en_proceso', // ya consumimos y producimos
                'is_active' => true,
            ]);

            // 3) Consumir lotes por cada ingrediente (PEPS asc)
            foreach ($producto->recetas as $receta) {
                $remaining = $receta->cant_x_unidad * $data['cantidad_a_producir'];
                $lotes = LoteInsumo::where('ingrediente_id', $receta->ingrediente_id)
                    ->where('cantidad_disponible_x_unidad', '>', 0)
                    ->orderBy('fecha_ingreso', 'ASC')
                    ->lockForUpdate()
                    ->get();

                foreach ($lotes as $lote) {
                    if ($remaining <= 0) break;
                    $consume = min($lote->cantidad_disponible_x_unidad, $remaining);

                    // decrementar lote
                    $lote->cantidad_disponible_x_unidad -= $consume;
                    $lote->save();

                    // registro de consumo
                    ConsumoInsumo::create([
                        'orden_produccion_id' => $orden->id,
                        'lote_insumo_id' => $lote->id,
                        'ingrediente_id' => $receta->ingrediente_id,
                        'cantidad_consumida' => $consume,
                    ]);

                    // movimiento global de auditoría (salida)
                    MovimientoInsumo::create([
                        'fecha' => now(),
                        'cantidad' => $consume,
                        'tipo_movimiento' => 'salida',
                        'motivo_movimiento' => "Producción Orden #{$orden->id}",
                        'ingrediente_id' => $receta->ingrediente_id,
                        'lote_insumo_id' => $lote->id,
                    ]);

                    $remaining -= $consume;
                }

                if ($remaining > 0) {
                    throw new \Exception("Error de concurrencia: no se pudo cubrir todo el ingrediente {$receta->ingrediente->nombre}.");
                }
            }

            // 4) Registrar producto terminado y movimiento de producto (entrada)
            ProductoTerminado::create([
                'producto_id' => $orden->producto_id,
                'orden_produccion_id' => $orden->id,
                'cantidad_producida' => $orden->cantidad_a_producir,
                'fecha_produccion' => now(),
            ]);

            MovimientoProducto::create([
                'producto_id' => $orden->producto_id,
                'orden_produccion_id' => $orden->id,
                'tipo_movimiento' => 'entrada',  // Cambiado de 'tipo' a 'tipo_movimiento'
                'cantidad' => $orden->cantidad_a_producir,
                'fecha' => now(),
            ]);

            DB::commit();
            return redirect()->route('ordenes.index')->with('success', "Orden #{$orden->id} creada y materiales consumidos.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $orden = OrdenProduccion::with(['producto.recetas.ingrediente', 'operario'])->findOrFail($id);

        // calcular costo estimado usando receta y precio medio por insumo (ejemplo)
        $estimatedCost = 0;
        if ($orden->producto && $orden->producto->recetas) {
            foreach ($orden->producto->recetas as $receta) {
                $avgPrice = LoteInsumo::where('ingrediente_id', $receta->ingrediente_id)->avg('costo_unitario') ?? 0;
                $estimatedCost += ($avgPrice * $receta->cant_x_unidad) * $orden->cantidad_a_producir;
            }
        }

        return Inertia::render('Produccion/Ordenes/Show', [
            'orden' => $orden,
            'estimatedCost' => round($estimatedCost, 2),
        ]);
    }

    /**
     * 🔵 MOMENTO 2: EJECUCIÓN (Procesar Consumo con PEPS/FIFO)
     * Aquí se descuenta el inventario usando el algoritmo PEPS estricto.
     * 
     * Este método se llama cuando el estado pasa de PENDIENTE → EN_PROCESO
     */
    public function ejecutarProduccion(Request $request, $id)
    {
        $orden = OrdenProduccion::with(['producto.recetas.ingrediente'])->findOrFail($id);

        // ✅ Validación de Estado
        if ($orden->estado !== 'pendiente') {
            return back()->withErrors(['error' => 'Esta orden ya fue procesada o no está pendiente.']);
        }

        // ✅ Obtener Receta
        $receta = $orden->producto->recetas()->first();
        if (!$receta) {
            return back()->withErrors(['error' => 'No se encontró receta para este producto.']);
        }

        DB::beginTransaction();
        try {
            // 🔍 PASO 1: Validación previa de stock (sin modificar nada aún)
            foreach ($receta->recetaItems as $item) {
                $cantidadNecesaria = $item->cantidad * $orden->cantidad_a_producir;
                $stockDisponible = LoteInsumo::where('ingrediente_id', $item->ingrediente_id)
                    ->where('cantidad_disponible_x_unidad', '>', 0)
                    ->sum('cantidad_disponible_x_unidad');

                if ($stockDisponible < $cantidadNecesaria) {
                    throw new \Exception("Stock insuficiente para {$item->ingrediente->nombre}. Necesitas {$cantidadNecesaria} pero solo hay {$stockDisponible}.");
                }
            }

            // 🔥 PASO 2: Aplicar el Algoritmo PEPS (FIFO) - Consumo Real
            foreach ($receta->recetaItems as $item) {
                $cantidadPendiente = $item->cantidad * $orden->cantidad_a_producir;
                $ingredienteId = $item->ingrediente_id;

                // 📦 Obtener lotes ordenados por fecha de ingreso (PEPS = ASC)
                $lotes = LoteInsumo::where('ingrediente_id', $ingredienteId)
                    ->where('cantidad_disponible_x_unidad', '>', 0)
                    ->orderBy('fecha_ingreso', 'ASC') // ⚠️ CRÍTICO: Los más antiguos primero
                    ->lockForUpdate() // Bloqueo para evitar condiciones de carrera
                    ->get();

                // 🔁 Bucle de Descuento
                foreach ($lotes as $lote) {
                    if ($cantidadPendiente <= 0) break; // Ya se cubrió la necesidad

                    // Calcular cuánto se puede tomar de este lote
                    $cantidadAConsumir = min($lote->cantidad_disponible_x_unidad, $cantidadPendiente);

                    // ✅ Restar del lote
                    $lote->cantidad_disponible_x_unidad -= $cantidadAConsumir;
                    $lote->save();

                    // 📝 Registrar el consumo específico (TRAZABILIDAD)
                    ConsumoInsumo::create([
                        'orden_produccion_id' => $orden->id,
                        'lote_insumo_id' => $lote->id,
                        'ingrediente_id' => $ingredienteId,
                        'cantidad_consumida' => $cantidadAConsumir,
                    ]);

                    // 📊 Registrar movimiento global de auditoría
                    MovimientoInsumo::create([
                        'ingrediente_id' => $ingredienteId,
                        'lote_insumo_id' => $lote->id,
                        'tipo_movimiento' => 'salida',
                        'motivo_movimiento' => "Producción Orden #{$orden->id}",
                        'cantidad' => $cantidadAConsumir,
                        'fecha' => now(),
                    ]);

                    // Restar de lo que aún falta
                    $cantidadPendiente -= $cantidadAConsumir;
                }

                // ❌ Si después del bucle aún falta algo, hubo un error de concurrencia
                if ($cantidadPendiente > 0) {
                    throw new \Exception("Error de concurrencia: No se pudo cubrir todo el ingrediente {$item->ingrediente->nombre}.");
                }
            }

            // ✅ Actualizar estado de la orden
            $orden->estado = 'en_proceso';
            $orden->save();

            DB::commit();
            return redirect()->route('ordenes.index')->with('success', "Producción iniciada. Materiales consumidos bajo método PEPS.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * 🟣 MOMENTO 3: FINALIZACIÓN (Terminar Orden)
     * Registra el producto terminado en el inventario de venta.
     */
    public function finalizarOrden(Request $request, $id)
    {
        $orden = OrdenProduccion::findOrFail($id);

        // ✅ Validación de Estado
        if ($orden->estado !== 'en_proceso') {
            return back()->withErrors(['error' => 'Esta orden no está en proceso. Primero debe ejecutar la producción.']);
        }

        DB::beginTransaction();
        try {
            // 📦 Registrar producto terminado
            ProductoTerminado::create([
                'producto_id' => $orden->producto_id,
                'orden_produccion_id' => $orden->id,
                'cantidad' => $orden->cantidad_a_producir,
            ]);

            // 📊 Registrar movimiento de entrada de producto
            MovimientoProducto::create([
                'producto_id' => $orden->producto_id,
                'orden_produccion_id' => $orden->id,
                'tipo' => 'ENTRADA',
                'cantidad' => $orden->cantidad_a_producir,
                'fecha' => now(),
            ]);

            // ✅ Marcar orden como finalizada
            $orden->estado = 'finalizada';
            $orden->save();

            DB::commit();
            return redirect()->route('ordenes.index')->with('success', "Orden #{$orden->id} finalizada. Producto ingresado al inventario de venta.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $orden = OrdenProduccion::findOrFail($id);
        $action = $request->input('action');

        // Delegar a los métodos especializados según la acción
        if ($action === 'ejecutar') {
            return $this->ejecutarProduccion($request, $id);
        }

        if ($action === 'finalizar') {
            return $this->finalizarOrden($request, $id);
        }

        // Edición normal de campos
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad_a_producir' => 'required|numeric|min:1',
            'operario_id' => 'nullable|exists:operarios,id',
            'fecha_creacion' => 'nullable|date',
        ]);

        $orden->update($validated);
        return redirect()->back()->with('success', 'Orden actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $orden = OrdenProduccion::findOrFail($id);
        $orden->delete();
        return redirect()->route('ordenes.index')->with('success', 'Orden eliminada correctamente');
    }

    /**
     * Consume materials according to recipe and method (PEPS/UEPS).
     * This function runs inside a transaction and records consumptions and movements.
     */
    protected function consumeMaterials(OrdenProduccion $orden, $metodo = 'peps')
    {
        DB::transaction(function () use ($orden, $metodo) {
            // load recipe items
            $producto = Producto::with('recetas.recetaItems')->findOrFail($orden->producto_id);
            $receta = $producto->recetas()->first();
            if (!$receta) {
                throw new \Exception('No se encontró receta para el producto.');
            }

            // for each receta item calculate total needed = item.cantidad * orden->cantidad_a_producir
            foreach ($receta->recetaItems as $item) {
                $needed = $item->cantidad * $orden->cantidad_a_producir;
                $ingredienteId = $item->ingrediente_id;

                // query lots ordered by fecha_ingreso asc (PEPS) or desc (UEPS)
                $orderDirection = strtolower($metodo) === 'ueps' ? 'desc' : 'asc';

                $lotes = LoteInsumo::where('ingrediente_id', $ingredienteId)
                    ->where('cantidad_disponible', '>', 0)
                    ->orderBy('fecha_ingreso', $orderDirection)
                    ->lockForUpdate()
                    ->get();

                if ($lotes->sum('cantidad_disponible') < $needed) {
                    throw new \Exception("Stock insuficiente para ingrediente ID {$ingredienteId}");
                }

                $remaining = $needed;
                foreach ($lotes as $lote) {
                    if ($remaining <= 0) break;
                    $consume = min($lote->cantidad_disponible, $remaining);

                    // reduce lote disponible
                    $lote->cantidad_disponible -= $consume;
                    $lote->save();

                    // create consumo_insumo record
                    ConsumoInsumo::create([
                        'orden_produccion_id' => $orden->id,
                        'lote_insumo_id' => $lote->id,
                        'ingrediente_id' => $ingredienteId,
                        'cantidad' => $consume,
                        'precio_unitario' => $lote->precio_unitario ?? 0,
                    ]);

                    // movimiento insumo (SALIDA)
                    MovimientoInsumo::create([
                        'lote_insumo_id' => $lote->id,
                        'tipo' => 'SALIDA',
                        'cantidad' => $consume,
                        'fecha' => now(),
                        'orden_produccion_id' => $orden->id,
                    ]);

                    $remaining -= $consume;
                }
            }

            // mark orden as in process (if not already)
            if ($orden->estado !== 'en_proceso') {
                $orden->estado = 'en_proceso';
                $orden->save();
            }
        });
    }
}

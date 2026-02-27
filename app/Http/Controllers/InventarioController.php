<?php

namespace App\Http\Controllers;

use App\Models\Ingrediente;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ProductoBase;
use App\Models\Proveedor;
use App\Models\LoteInsumo;


class InventarioController extends Controller
{
    public function index()
    {
        $productos = ProductoBase::all();
        return Inertia::render('Inventario/index', [
            'productos' => $productos,
        ]);
    }
    public function indexAlmacen(Request $request)
    {
        $query = Ingrediente::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'LIKE', "%{$search}%");
        }

        $ingredientes = $query->get();
        // La variable $ingredientes debe contener todos los registros de la tabla 'ingredientes'.
        // Ejemplo de estructura esperada para un ingrediente:
        // [
        //     'id' => 1,
        //     'nombre' => 'Harina',
        //     'unidad_medida' => 'Kg',
        //     'descripcion' => 'Harina de trigo para panadería',
        //     'is_active' => true,
        //     'created_at' => '2023-01-01 10:00:00',
        //     'updated_at' => '2023-01-01 10:00:00',
        // ]
        return Inertia::render('Inventario/Almacen/index', [
            'ingredientes' => $ingredientes,
            'filters' => $request->only(['search']),
        ]);
    }
    public function createIngrediente($idIngrediente)
    {
        $proveedores = Proveedor::all();
        // La variable $proveedores debe contener todos los registros de la tabla 'proveedors'.
        // Ejemplo de estructura esperada para un proveedor:
        // [
        //     'id' => 1,
        //     'nombre' => 'Proveedor A',
        //     'email' => 'proveedorA@example.com',
        //     'telefono' => '123456789',
        //     'estado' => true,
        //     'created_at' => '2023-01-01 10:00:00',
        //     'updated_at' => '2023-01-01 10:00:00',
        // ]
        $ingrediente = Ingrediente::find($idIngrediente);
        return Inertia::render('Inventario/Almacen/Ingredientes/create', [
            'ingrediente' => $ingrediente,
            'proveedores' => $proveedores,
        ]);
    }
    public function entradaCreate($idIngrediente)
    {
        $ingrediente = Ingrediente::find($idIngrediente);
        $proveedores = Proveedor::all();
        return Inertia::render('Inventario/Almacen/Ingredientes/entrada', [
            'ingrediente' => $ingrediente,
            'proveedores' => $proveedores,
        ]);

    }
    public function entradaStore(Request $request)
    {
        // Lógica para almacenar la entrada de ingrediente
        $request->validate([
            'ingrediente_id' => 'required|exists:ingredientes,id',
            'proveedor_id' => 'nullable|exists:proveedors,id',
            'cantidad_total_x_unidad' => 'required|numeric|min:0',
            'costo_unitario' => 'required|numeric|min:0',
            'costo_lote' => 'required|numeric|min:0',
        ]);

        $ingrediente = Ingrediente::find($request->ingrediente_id);
        $loteInsumo = $ingrediente->lotes()->create([
            'fecha_ingreso' => now(),
            'cantidad_total_x_unidad' => $request->cantidad_total_x_unidad,
            'cantidad_disponible_x_unidad' => $request->cantidad_total_x_unidad, // Al inicio, todo está disponible
            'costo_unitario' => $request->costo_unitario,
            'costo_lote' => $request->costo_lote,
            'proveedor_id' => $request->proveedor_id,
            'ingrediente_id' => $request->ingrediente_id,
        ]);

        // Registrar movimiento de entrada en MovimientoInsumo
        $loteInsumo->movimientos()->create([
            'fecha' => now(),
            'cantidad' => $request->cantidad_total_x_unidad,
            'tipo_movimiento' => 'entrada',
            'motivo_movimiento' => 'compra',
            'ingrediente_id' => $request->ingrediente_id,
            'lote_insumo_id' => $loteInsumo->id,
        ]);


        return redirect()->route('almacen.index')
            ->with('success', 'Entrada de ' . $ingrediente->nombre . ' registrada exitosamente.');

    }
    public function salidaCreate($idIngrediente)
    {
        $ingrediente = Ingrediente::findOrFail($idIngrediente);
        // Cargar solo los lotes que tienen cantidad disponible.
        $lotes = $ingrediente->lotes()->where('cantidad_disponible_x_unidad', '>', 0)->orderBy('fecha_ingreso', 'asc')->get();

        return Inertia::render('Inventario/Almacen/Ingredientes/salida', [
            'ingrediente' => $ingrediente,
            'lotes' => $lotes,
        ]);
    }

    public function salidaStore(Request $request)
    {
        $request->validate([
            'ingrediente_id' => 'required|exists:ingredientes,id',
            'lote_insumo_id' => 'required|exists:lote_insumos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo_movimiento' => 'required|string|in:Producción,Merma,Ajuste',
        ]);

        $lote = LoteInsumo::findOrFail($request->lote_insumo_id);

        // Validar que la cantidad a retirar no sea mayor a la disponible
        if ($request->cantidad > $lote->cantidad_disponible_x_unidad) {
            return back()->withErrors(['cantidad' => 'La cantidad a retirar no puede ser mayor a la disponible en el lote.']);
        }

        // Crear el movimiento de salida
        $lote->movimientos()->create([
            'fecha' => now(),
            'cantidad' => $request->cantidad,
            'tipo_movimiento' => 'salida',
            'motivo_movimiento' => $request->motivo_movimiento,
            'ingrediente_id' => $request->ingrediente_id,
        ]);

        $lote->decrement('cantidad_disponible_x_unidad', $request->cantidad);

        return redirect()->route('almacen.index')->with('success', 'Salida de insumo registrada correctamente.');
    }

}

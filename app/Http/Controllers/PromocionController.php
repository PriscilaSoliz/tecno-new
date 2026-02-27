<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'precio' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Desactivar otras promociones activas para este producto
            Promocion::where('producto_id', $request->producto_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $promocion = Promocion::create($request->all());

            // Actualizar estado del producto
            $this->actualizarEstadoProducto($request->producto_id);

            DB::commit();

            return back()->with('success', 'Promoción creada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la promoción: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $promocion = Promocion::findOrFail($id);

        $request->validate([
            'precio' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            if ($request->is_active) {
                // Desactivar otras promociones si esta se activa
                Promocion::where('producto_id', $promocion->producto_id)
                    ->where('id', '!=', $id)
                    ->update(['is_active' => false]);
            }

            $promocion->update($request->all());

            // Actualizar estado del producto
            $this->actualizarEstadoProducto($promocion->producto_id);

            DB::commit();

            return back()->with('success', 'Promoción actualizada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la promoción: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $promocion = Promocion::findOrFail($id);
        $productoId = $promocion->producto_id;
        $promocion->delete();

        $this->actualizarEstadoProducto($productoId);

        return back()->with('success', 'Promoción eliminada correctamente');
    }

    private function actualizarEstadoProducto($productoId)
    {
        $producto = Producto::find($productoId);
        if ($producto) {
            // Verificar si tiene alguna promoción activa y vigente
            $tienePromo = Promocion::where('producto_id', $productoId)
                ->where('is_active', true)
                ->whereDate('fecha_inicio', '<=', now())
                ->whereDate('fecha_fin', '>=', now())
                ->exists();

            $producto->update(['has_promo' => $tienePromo]);
        }
    }
}

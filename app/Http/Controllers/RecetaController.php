<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use App\Models\Producto;
use App\Models\Ingrediente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RecetaController extends Controller
{
	// helper to pick a display name from possible column names
	protected function pickName($model)
	{
		if (! $model) return null;
		return $model->name ?? $model->nombre ?? $model->titulo ?? $model->descripcion ?? $model->producto ?? $model->title ?? null;
	}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recetasRaw = Receta::with(['producto', 'ingrediente'])->orderBy('id', 'desc')->get();

        // normalize recetas so producto/ingrediente have 'name'
        $recetas = $recetasRaw->map(function ($r) {
            return [
                'id' => $r->id,
                'producto_id' => $r->producto_id,
                'ingrediente_id' => $r->ingrediente_id,
                'cant_x_unidad' => $r->cant_x_unidad,
                'producto' => $r->producto ? [
                    'id' => $r->producto->id,
                    'name' => $this->pickName($r->producto),
                ] : null,
                'ingrediente' => $r->ingrediente ? [
                    'id' => $r->ingrediente->id,
                    'name' => $this->pickName($r->ingrediente),
                ] : null,
            ];
        });

        // fetch productos & ingredientes and map to {id, name} (avoid assuming 'name' column)
        $productos = Producto::all()->map(function ($p) {
            return ['id' => $p->id, 'name' => $this->pickName($p)];
        });

        $ingredientes = Ingrediente::all()->map(function ($i) {
            return ['id' => $i->id, 'name' => $this->pickName($i)];
        });

        return Inertia::render('Produccion/Recetas/index', [
            'recetas' => $recetas,
            'productos' => $productos,
            'ingredientes' => $ingredientes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productos = Producto::all()->map(function ($p) {
            return ['id' => $p->id, 'name' => $this->pickName($p)];
        });
        $ingredientes = Ingrediente::all()->map(function ($i) {
            return ['id' => $i->id, 'name' => $this->pickName($i)];
        });

        return Inertia::render('Produccion/Recetas/Create', [
            'productos' => $productos,
            'ingredientes' => $ingredientes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'ingrediente_id' => 'required|exists:ingredientes,id',
            'cant_x_unidad' => 'required|numeric|min:0.01',
        ]);

        $exists = Receta::where('producto_id', $validated['producto_id'])
                        ->where('ingrediente_id', $validated['ingrediente_id'])
                        ->exists();

        if ($exists) {
            return back()->withErrors(['general' => 'Ya existe una receta para este producto e ingrediente.'])->withInput();
        }

        Receta::create($validated);

        return redirect()->route('recetas.index')->with('success', 'Receta creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Not implemented, as not used in the frontend
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not implemented, as editing is done via modal in index
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $receta = Receta::findOrFail($id);

        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'ingrediente_id' => 'required|exists:ingredientes,id',
            'cant_x_unidad' => 'required|numeric|min:0.01',
        ]);

        // Check for uniqueness (excluding current)
        $exists = Receta::where('producto_id', $validated['producto_id'])
                        ->where('ingrediente_id', $validated['ingrediente_id'])
                        ->where('id', '!=', $id)
                        ->exists();
        
        if ($exists) {
            return back()->withErrors([
                'general' => 'Ya existe una receta para este producto e ingrediente.'
            ])->withInput();
        }

        $receta->update($validated);

        return redirect()->route('recetas.index')
            ->with('success', 'Receta actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $receta = Receta::findOrFail($id);
        $receta->delete();

        return redirect()->route('recetas.index')->with('success', 'Receta eliminada exitosamente.');
    }
}

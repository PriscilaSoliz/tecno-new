<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function topProducts()
    {
        $threeDaysAgo = Carbon::now()->subDays(3);
        $topProducts = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->select('productos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as total'))
            ->where('detalle_ventas.created_at', '>=', $threeDaysAgo)
            ->groupBy('productos.nombre')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return response()->json($topProducts);
    }

    public function salesTimeline()
    {
        $tenDaysAgo = Carbon::now()->subDays(10);
        $salesTimeline = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->select(DB::raw('DATE(detalle_ventas.created_at) as date'), 'productos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as total'))
            ->where('detalle_ventas.created_at', '>=', $tenDaysAgo)
            ->groupBy('date', 'productos.nombre')
            ->orderBy('date')
            ->get();

        return response()->json($salesTimeline);
    }
}

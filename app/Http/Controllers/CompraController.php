<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use Inertia\Inertia;

class CompraController extends Controller
{
    public function index()
    {
        $usuarioId = auth()->id();

        $enCurso = Pedido::where('cliente_id', $usuarioId)
            ->whereIn('estado_produccion', ['pending','assigned','on_route'])
            ->orderBy('fecha','desc')
            ->get();

        $realizados = Pedido::where('cliente_id', $usuarioId)
            ->where('estado_produccion', 'delivered')
            ->orderBy('fecha','desc')
            ->get();

        return Inertia::render('Pedidos/Cliente/index', [
            'enCurso' => $enCurso,
            'realizados' => $realizados,
        ]);
    }

    public function show($id)
    {
        $pedido = Pedido::where('cliente_id', auth()->id())->findOrFail($id);
        $detalles = PedidoDetalle::with('producto')->where('pedido_id', $pedido->id)->get();

        return Inertia::render('Pedidos/Cliente/EnCurso', [
            'pedido' => $pedido,
            'detalles' => $detalles,
        ]);
    }

    public function marcarRecibido($id)
    {
        $pedido = Pedido::where('cliente_id', auth()->id())->findOrFail($id);
        $pedido->estado_produccion = 'delivered';
        $pedido->save();

        // Mostrar pantalla final y luego volver al índice
        return Inertia::render('Pedidos/Cliente/DetalleFinal', [
            'pedido' => $pedido,
        ]);
    }
}

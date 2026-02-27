<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\PedidoRastreo;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PedidoController extends Controller
{
    /**
     * Vista del propietario - lista todos los pedidos
     */
    public function index()
    {
        $pedidos = Pedido::with(['cliente.user', 'detalles.producto'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Pedidos/index', [
            'pedidos' => $pedidos
        ]);
    }

    /**
     * Vista del propietario - detalle de pedido con mapa
     */
    public function show($id)
    {
        $pedido = Pedido::with(['cliente.user', 'detalles.producto', 'venta.cuotas'])->findOrFail($id);
        $detalles = $pedido->detalles;

        // Obtener rastreo para el mapa
        $rastreo = PedidoRastreo::where('pedido_id', $id)->latest()->first();

        // Ubicación de la tienda (fija o configurable)
        $tiendaLat = -17.7833; // Santa Cruz, Bolivia ejemplo
        $tiendaLng = -63.1821;

        $route = [
            'origin' => ['lat' => $tiendaLat, 'lng' => $tiendaLng],
            'destination' => [
                'lat' => $rastreo ? $rastreo->latitud : $tiendaLat,
                'lng' => $rastreo ? $rastreo->longitud : $tiendaLng
            ]
        ];

        return Inertia::render('Pedidos/show', [
            'pedido' => $pedido,
            'detalles' => $detalles,
            'deliveryRoute' => $route
        ]);
    }

    /**
     * Actualizar estado del pedido (propietario/delivery)
     */
    public function update(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        $data = $request->validate([
            'estado_produccion' => 'nullable|in:pending,assigned,on_route,delivered,completed',
        ]);

        if (isset($data['estado_produccion'])) {
            $pedido->estado_produccion = $data['estado_produccion'];
            $pedido->save();
        }

        return redirect()->back()->with('success', 'Estado actualizado');
    }

    /**
     * Acción rápida para avanzar al siguiente estado
     */
    public function quickComplete($id)
    {
        $pedido = Pedido::findOrFail($id);

        $estados = ['pending', 'assigned', 'on_route', 'delivered', 'completed'];
        $currentIndex = array_search($pedido->estado_produccion, $estados);

        if ($currentIndex !== false && $currentIndex < count($estados) - 1) {
            $pedido->estado_produccion = $estados[$currentIndex + 1];
            $pedido->save();
        }

        return redirect()->back()->with('success', 'Estado avanzado');
    }

    /**
     * Vista del cliente - sus pedidos
     */
    public function clienteIndex()
    {
        $user = auth()->user();
        $clienteId = $user->cliente->id ?? null;

        if (!$clienteId) {
            return Inertia::render('Pedidos/Cliente/index', [
                'enCurso' => [],
                'realizados' => []
            ]);
        }

        $pedidos = Pedido::with(['detalles.producto', 'venta.cuotas'])
            ->where('cliente_id', $clienteId)
            ->orderBy('created_at', 'desc')
            ->get();

        $enCurso = $pedidos->filter(fn($p) => !in_array($p->estado_produccion, ['delivered', 'completed']));
        $realizados = $pedidos->filter(fn($p) => in_array($p->estado_produccion, ['delivered', 'completed']));

        return Inertia::render('Pedidos/Cliente/index', [
            'enCurso' => $enCurso->values(),
            'realizados' => $realizados->values()
        ]);
    }

    /**
     * Vista del cliente - detalle de su pedido
     */
    public function clienteShow($id)
    {
        $user = auth()->user();
        $pedido = Pedido::with(['cliente.user', 'detalles.producto', 'venta.cuotas'])->findOrFail($id);

        // Verificar que el pedido pertenece al cliente
        if ($pedido->cliente_id !== ($user->cliente->id ?? null)) {
            abort(403);
        }

        return Inertia::render('Pedidos/Cliente/EnCurso', [
            'pedido' => $pedido,
            'detalles' => $pedido->detalles
        ]);
    }

    /**
     * Cliente marca pedido como recibido
     */
    public function clienteRecibido($id)
    {
        $user = auth()->user();
        $pedido = Pedido::findOrFail($id);

        if ($pedido->cliente_id !== ($user->cliente->id ?? null)) {
            abort(403);
        }

        $pedido->estado_produccion = 'completed';
        $pedido->save();

        return redirect()->route('cliente.pedidos.index')
            ->with('success', 'Pedido marcado como recibido');
    }

    /**
     * Pagar una cuota específica
     */
    public function pagarCuota(Request $request, $id)
    {
        $cuota = \App\Models\VentaCuota::findOrFail($id);

        // Validar que pertenezca al usuario (indirectamente a través de Venta->Pedido->Cliente)
        $user = auth()->user();
        if ($cuota->venta->pedido->cliente_id !== ($user->cliente->id ?? null)) {
            abort(403);
        }

        $cuota->update([
            'estado' => 'pagado',
            'fecha_pago' => now(),
            // Podríamos guardar el transaction_id si agregamos columna
        ]);

        return redirect()->back()->with('success', 'Pago registrado correctamente');
    }
}

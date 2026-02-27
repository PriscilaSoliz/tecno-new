<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CatalogoController extends Controller
{
    public function index()
    {
        // Obtener productos activos con los campos necesarios para el catálogo
        $productos = Producto::where('is_active', true)
            ->select(['id', 'nombre', 'precio_venta', 'descripcion', 'imagen', 'unidad_medida', 'has_promo'])
            ->with([
                'promocion' => function ($query) {
                    $query->where('is_active', true)
                        ->whereDate('fecha_inicio', '<=', now())
                        ->whereDate('fecha_fin', '>=', now());
                }
            ])
            ->orderBy('nombre', 'asc')
            ->get();

        return Inertia::render('Catalogo/index', [
            'productos' => $productos
        ]);
    }

    public function venta(Request $request)
    {
        // Si es GET y no hay productos, redirigir al catálogo
        if ($request->isMethod('get') && empty($request->input('productos'))) {
            return redirect()->route('catalogo.index')->with('info', 'Por favor, selecciona productos desde el catálogo');
        }

        // Recibir datos del carrito (POST) o mostrar vista vacía (GET)
        $productos = $request->input('productos', []);
        $total = $request->input('total');

        // Si no se envió total, calcularlo
        if ($total === null && !empty($productos)) {
            $total = collect($productos)->sum(function ($item) {
                return ($item['precio'] ?? 0) * ($item['cantidad'] ?? 1);
            });
        }

        return Inertia::render('Catalogo/Venta/index', [
            'productos' => $productos,
            'cliente' => Auth::user(),
            'total' => (float) ($total ?? 0)
        ]);
    }

    /**
     * Confirmar pedido - Crea Pedido, PedidoDetalle, Venta, DetalleVenta
     */
    public function confirmar(Request $request)
    {
        $request->validate([
            'productos' => 'required|array|min:1',
            'tipo_pago' => 'required|string',
            'modalidad_pago' => 'required|string',
            'cuotas_schedule' => 'nullable|array',
            'pago_inicial' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $total = $request->input('total');
            $pagoInicial = $request->input('pago_inicial', $total); // Default total if not set

            \Log::info('Confirmando pedido para usuario', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'roles' => $user->getRoleNames()->toArray(),
                'total' => $total,
            ]);

            // Asegurar que existe el registro de Cliente para este usuario
            $cliente = $user->cliente;
            if (!$cliente) {
                \Log::info('Creando nuevo registro de Cliente para usuario', ['user_id' => $user->id]);
                // La tabla clientes solo tiene: user_id, nit, razon_social
                $cliente = Cliente::create([
                    'user_id' => $user->id,
                    'nit' => null, // Opcional
                    'razon_social' => null, // Opcional
                ]);
            }
            $clienteId = $cliente->id;

            \Log::info('Cliente asociado', ['cliente_id' => $clienteId]);

            // 1. Crear Pedido
            $pedido = Pedido::create([
                'fecha' => now(),
                'total' => $total,
                'estado_produccion' => 'pending',
                'cliente_id' => $clienteId,
                'ubicacion_id' => $user->ubicacion ? $user->ubicacion->id : null,
            ]);

            \Log::info('Pedido creado', ['pedido_id' => $pedido->id]);

            // 2. Crear Detalles del Pedido
            foreach ($request->productos as $prod) {
                PedidoDetalle::create([
                    'cantidad' => $prod['cantidad'],
                    'precio_unitario' => $prod['precio'],
                    'subtotal' => $prod['precio'] * $prod['cantidad'],
                    'pedido_id' => $pedido->id,
                    'producto_id' => $prod['id'],
                ]);
            }

            // 3. Registrar Venta
            $venta = Venta::create([
                'fecha' => now(),
                'total' => $total,
                'tipo_pago' => $request->tipo_pago,
                'modo_pago' => $request->modalidad_pago,
                'pedido_id' => $pedido->id,
                'cliente_id' => $clienteId,
            ]);

            \Log::info('Venta creada', ['venta_id' => $venta->id]);

            // 4. Registrar Cuotas (si aplica)
            if ($request->modalidad_pago === 'cuotas' && !empty($request->cuotas_schedule)) {
                $cuotas = $request->cuotas_schedule;
                foreach ($cuotas as $c) {
                    $venta->cuotas()->create([
                        'numero_cuota' => $c['numero'],
                        'monto' => $c['monto'],
                        'fecha_vencimiento' => $c['fecha'],
                        'fecha_pago' => $c['numero'] === 1 ? now() : null,
                        'estado' => $c['numero'] === 1 ? 'pagado' : 'pendiente',
                    ]);
                }
            }

            // 5. Registrar Detalles de Venta
            foreach ($request->productos as $prod) {
                DetalleVenta::create([
                    'cantidad' => $prod['cantidad'],
                    'precio_unitario' => $prod['precio'],
                    'subtotal' => $prod['precio'] * $prod['cantidad'],
                    'venta_id' => $venta->id,
                    'producto_id' => $prod['id'],
                ]);
            }

            DB::commit();

            \Log::info('Pedido confirmado exitosamente', [
                'pedido_id' => $pedido->id,
                'venta_id' => $venta->id,
                'tipo_pago' => $request->tipo_pago,
            ]);

            // Si es pago por QR
            if ($request->tipo_pago === 'qr') {
                return Inertia::render('Catalogo/Venta/QRPago', [
                    'venta_id' => $venta->id,
                    'total' => (float) $pagoInicial, // Usar pago inicial para el QR
                    'productos' => $request->productos,
                    'cliente' => $user,
                    'es_cuota' => $request->modalidad_pago === 'cuotas',
                ]);
            }

            // Redirigir a mis pedidos (disponible para todos los roles autenticados)
            return redirect()->route('cliente.pedidos.index')->with('success', 'Pedido realizado con éxito');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al confirmar pedido', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);
            return back()->withErrors(['error' => 'Error al procesar el pedido: ' . $e->getMessage()]);
        }
    }
}

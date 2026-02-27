<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Producto;
use App\Models\Ingrediente;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $results = collect();
        $user = Auth::user();
        $userRoles = $user->getRoleNames()->toArray();

        // Determinar si es cliente
        $isCliente = in_array('cliente', $userRoles);
        $isPropietario = in_array('propietario', $userRoles);
        $isEncargadoAlmacen = in_array('encargadoalmacen', $userRoles);
        $isProduccion = in_array('produccion', $userRoles);

        // 1. Usuarios - Solo propietario
        if ($isPropietario) {
            $users = User::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->name,
                        'subtitle' => $item->email,
                        'category' => 'Usuarios',
                        'icon' => 'fas fa-user',
                        'url' => route('users.index', ['search' => $item->name]),
                    ];
                });
            $results = $results->merge($users);
        }

        // 2. Productos
        $products = Producto::where('nombre', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($item) use ($isCliente) {
                // Clientes van al catálogo, otros roles van a administración
                $url = $isCliente
                    ? route('catalogo.index') . '?search=' . urlencode($item->nombre)
                    : route('productos.index', ['search' => $item->nombre]);

                return [
                    'id' => $item->id,
                    'title' => $item->nombre,
                    'subtitle' => $isCliente
                        ? 'Ver en Catálogo - ' . $item->unidad_medida
                        : 'Producto - ' . $item->unidad_medida,
                    'category' => 'Productos',
                    'icon' => 'fas fa-box',
                    'url' => $url,
                ];
            });
        $results = $results->merge($products);

        // 3. Inventario (Ingredientes) - Solo propietario y encargado de almacén
        if ($isPropietario || $isEncargadoAlmacen) {
            $ingredients = Ingrediente::where('nombre', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->nombre,
                        'subtitle' => 'Ingrediente/Insumo',
                        'category' => 'Inventario',
                        'icon' => 'fas fa-boxes',
                        'url' => route('almacen.index', ['search' => $item->nombre]),
                    ];
                });
            $results = $results->merge($ingredients);
        }

        // 4. Pedidos
        if ($isCliente) {
            // Clientes: Solo sus propios pedidos
            $pedidos = Pedido::with('cliente.user')
                ->whereHas('cliente', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->where(function ($q) use ($query) {
                    $q->where('id', 'LIKE', "%{$query}%")
                        ->orWhere('estado_produccion', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => "Mi Pedido #{$item->id}",
                        'subtitle' => "Estado: " . ucfirst($item->estado_produccion),
                        'category' => 'Mis Pedidos',
                        'icon' => 'fas fa-shopping-bag',
                        'url' => route('cliente.pedidos.show', $item->id),
                    ];
                });
            $results = $results->merge($pedidos);
        } else if ($isPropietario || $isEncargadoAlmacen || $isProduccion) {
            // Admin: Todos los pedidos
            $pedidos = Pedido::with('cliente.user')
                ->where('id', 'LIKE', "%{$query}%")
                ->orWhereHas('cliente.user', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $clientName = $item->cliente && $item->cliente->user
                        ? $item->cliente->user->name
                        : 'Cliente desconocido';
                    return [
                        'id' => $item->id,
                        'title' => "Pedido #{$item->id}",
                        'subtitle' => "De: {$clientName}",
                        'category' => 'Pedidos',
                        'icon' => 'fas fa-truck',
                        'url' => route('pedidos.show', $item->id),
                    ];
                });
            $results = $results->merge($pedidos);
        }

        return response()->json($results);
    }
}

<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        MenuItem::truncate();

        $items = [
            // Dashboard - solo para propietario y produccion (NO cliente ni encargadoalmacen)
            [
                'title' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'fas fa-home',
                'order' => 1,
                'is_active' => true,
                'roles' => json_encode(['propietario', 'produccion']),
            ],
            // Gestión de Usuarios - visible solo para propietario
            [
                'title' => 'Usuarios',
                'route' => 'users.index',
                'icon' => 'fas fa-users',
                'order' => 2,
                'is_active' => true,
                'roles' => json_encode(['propietario']),
            ],
            // Inventario - visible para encargadoalmacen y propietario
            [
                'title' => 'Inventario',
                'route' => 'inventory',
                'icon' => 'fas fa-boxes',
                'order' => 3,
                'is_active' => true,
                'roles' => json_encode(['propietario', 'encargadoalmacen']),
            ],
            // Producción - visible para produccion y propietario
            [
                'title' => 'Producción',
                'route' => 'production',
                'icon' => 'fas fa-bread-slice',
                'order' => 4,
                'is_active' => true,
                'roles' => json_encode(['propietario', 'produccion']),
            ],
            // Métodos y Estadísticas - visible para encargadoalmacen y propietario
            [
                'title' => 'Métodos y Estadísticas',
                'route' => 'metodos.index',
                'icon' => 'fas fa-calculator',
                'order' => 5,
                'is_active' => true,
                'roles' => json_encode(['propietario', 'encargadoalmacen']),
            ],
            // Catálogo - visible para cliente y propietario
            [
                'title' => 'Catálogo',
                'route' => 'catalogo.index',
                'icon' => 'fas fa-book',
                'order' => 6,
                'is_active' => true,
                'roles' => json_encode(['propietario', 'cliente']),
            ],
            // Mis Pedidos - visible para cliente y propietario
            [
                'title' => 'Mis Pedidos',
                'route' => 'cliente.pedidos.index',
                'icon' => 'fas fa-clipboard-list',
                'order' => 7,
                'is_active' => true,
                'roles' => json_encode(['propietario', 'cliente']),
            ],
            // Pedidos (gestión) - visible para propietario y encargadoalmacen
            [
                'title' => 'Pedidos',
                'route' => 'pedidos.index',
                'icon' => 'fas fa-truck',
                'order' => 8,
                'is_active' => true,
                'roles' => json_encode(['propietario', 'encargadoalmacen']),
            ],
        ];

        foreach ($items as $item) {
            MenuItem::create($item);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuarios base (admin/propietario)
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('123456789'),
        ]);
        
        User::factory()->create([
            'name' => 'Another User',
            'email' => 'another@example.com',
            'password' => bcrypt('123456789'),
        ]);

        // Llamar seeders en orden de dependencias
        $this->call(MenuItemSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ClienteSeeder::class);
        $this->call(ProveedorSeeder::class);

        // Datos base que tienen lógica entre ellos (ingredientes, lotes, productos, recetas, órdenes)
        // Estos se insertan directamente porque tienen relaciones complejas ya probadas

        DB::table('ingredientes')->insert([
            ['id' => 1, 'nombre' => 'Harina', 'unidad_medida' => 'kg', 'descripcion' => 'Harina de trigo', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Sal', 'unidad_medida' => 'kg', 'descripcion' => 'Sal de mesa', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Almidon', 'unidad_medida' => 'kg', 'descripcion' => 'Almidón de maíz', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('lote_insumos')->insert([
            ['id' => 1, 'fecha_ingreso' => now()->toDateString(), 'cantidad_total_x_unidad' => 100, 'cantidad_disponible_x_unidad' => 91.9, 'costo_unitario' => 8, 'costo_lote' => 800, 'proveedor_id' => 1, 'ingrediente_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'fecha_ingreso' => now()->toDateString(), 'cantidad_total_x_unidad' => 20, 'cantidad_disponible_x_unidad' => 20, 'costo_unitario' => 1, 'costo_lote' => 20, 'proveedor_id' => 1, 'ingrediente_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'fecha_ingreso' => now()->toDateString(), 'cantidad_total_x_unidad' => 50, 'cantidad_disponible_x_unidad' => 33.8, 'costo_unitario' => 8, 'costo_lote' => 400, 'proveedor_id' => 1, 'ingrediente_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'fecha_ingreso' => now()->toDateString(), 'cantidad_total_x_unidad' => 20, 'cantidad_disponible_x_unidad' => 20, 'costo_unitario' => 20, 'costo_lote' => 400, 'proveedor_id' => 1, 'ingrediente_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('movimiento_insumos')->insert([
            ['id' => 1, 'fecha' => now()->toDateString(), 'cantidad' => 100, 'tipo_movimiento' => 'entrada', 'motivo_movimiento' => 'compra', 'ingrediente_id' => 2, 'lote_insumo_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'fecha' => now()->toDateString(), 'cantidad' => 20, 'tipo_movimiento' => 'entrada', 'motivo_movimiento' => 'compra', 'ingrediente_id' => 2, 'lote_insumo_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'fecha' => now()->toDateString(), 'cantidad' => 50, 'tipo_movimiento' => 'entrada', 'motivo_movimiento' => 'compra', 'ingrediente_id' => 1, 'lote_insumo_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'fecha' => now()->toDateString(), 'cantidad' => 20, 'tipo_movimiento' => 'entrada', 'motivo_movimiento' => 'compra', 'ingrediente_id' => 3, 'lote_insumo_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('productos')->insert([
            ['id' => 1, 'nombre' => 'Pan Frances', 'unidad_medida' => 'unidad', 'precio_venta' => 0.50, 'descripcion' => 'Pan francés crujiente', 'imagen' => '/images/products/pan1.jpg', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Pan de batalla', 'unidad_medida' => 'unidad', 'precio_venta' => 0.50, 'descripcion' => 'Pan de batalla tradicional', 'imagen' => '/images/products/pan2.jpg', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('recetas')->insert([
            ['id' => 1, 'cant_x_unidad' => 0.02, 'producto_id' => 1, 'ingrediente_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'cant_x_unidad' => 0.01, 'producto_id' => 1, 'ingrediente_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'cant_x_unidad' => 0.01, 'producto_id' => 2, 'ingrediente_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'cant_x_unidad' => 0.02, 'producto_id' => 2, 'ingrediente_id' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('orden_produccions')->insert([
            ['id' => 1, 'fecha_creacion' => now()->toDateString(), 'cantidad_a_producir' => 10, 'estado' => 'en_proceso', 'operario_id' => null, 'producto_id' => 1, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'fecha_creacion' => now()->toDateString(), 'cantidad_a_producir' => 800, 'estado' => 'en_proceso', 'operario_id' => null, 'producto_id' => 2, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('consumo_insumos')->insert([
            ['id' => 1, 'cantidad_consumida' => 0.2, 'orden_produccion_id' => 1, 'ingrediente_id' => 1, 'lote_insumo_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'cantidad_consumida' => 0.1, 'orden_produccion_id' => 1, 'ingrediente_id' => 2, 'lote_insumo_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'cantidad_consumida' => 8, 'orden_produccion_id' => 2, 'ingrediente_id' => 2, 'lote_insumo_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'cantidad_consumida' => 16, 'orden_produccion_id' => 2, 'ingrediente_id' => 1, 'lote_insumo_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('producto_terminados')->insert([
            ['id' => 1, 'fecha_produccion' => now()->toDateString(), 'cantidad_producida' => 10, 'orden_produccion_id' => 1, 'producto_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'fecha_produccion' => now()->toDateString(), 'cantidad_producida' => 800, 'orden_produccion_id' => 2, 'producto_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('movimiento_productos')->insert([
            ['id' => 1, 'fecha' => now()->toDateString(), 'cantidad' => 10, 'tipo_movimiento' => 'entrada', 'motivo_movimiento' => null, 'producto_id' => 1, 'producto_terminado_id' => null, 'orden_produccion_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'fecha' => now()->toDateString(), 'cantidad' => 800, 'tipo_movimiento' => 'entrada', 'motivo_movimiento' => null, 'producto_id' => 2, 'producto_terminado_id' => null, 'orden_produccion_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Llamar seeders adicionales que dependen de los datos anteriores
        // $this->call(OperarioSeeder::class);
        // $this->call(PedidoSeeder::class);
        // $this->call(VentaSeeder::class);
    }
}

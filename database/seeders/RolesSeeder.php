<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear roles
        $propietario = Role::create(['name' => 'propietario']);
        $encargadoAlmacen = Role::create(['name' => 'encargadoalmacen']);
        $produccion = Role::create(['name' => 'produccion']);
        $cliente = Role::create(['name' => 'cliente']);

        // Crear permisos
        $permissions = [
            'gestionar inventario',
            'registrar ventas',
            'gestionar producción',
            'ver catálogo',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Asignar permisos a roles
        $propietario->givePermissionTo(Permission::all());
        $encargadoAlmacen->givePermissionTo(['gestionar inventario']);
        $produccion->givePermissionTo(['gestionar producción']);
        $cliente->givePermissionTo(['ver catálogo']);

        // Asignar rol a usuario
        $user = User::find(1);
        if ($user) {
            $user->assignRole('propietario');
        }
    }
}

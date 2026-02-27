<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clientes
        \App\Models\User::factory()->create([
            'name' => 'Cliente Uno',
            'email' => 'cliente1@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('cliente');

        \App\Models\User::factory()->create([
            'name' => 'Cliente Dos',
            'email' => 'cliente2@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('cliente');

        // Proveedores
        // \App\Models\User::factory()->create([
        //     'name' => 'Encargado Uno',
        //     'email' => 'Encargado1@example.com',
        //     'password' => bcrypt('password'),
        // ])->assignRole('encargadoalmacen');

        \App\Models\User::factory()->create([
            'name' => 'Encargado de Almacén',
            'email' => 'encargadoalmacen@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('encargadoalmacen');

        // Empleados (Producción)
        \App\Models\User::factory()->create([
            'name' => 'Empleado Uno',
            'email' => 'empleado1@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('produccion');

        \App\Models\User::factory()->create([
            'name' => 'Empleado Dos',
            'email' => 'empleado2@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('produccion');
        
        // Usuarios de operarios
        \App\Models\User::factory()->create([
            'name' => 'Operario Uno',
            'email' => 'operario1@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('produccion');

        \App\Models\User::factory()->create([
            'name' => 'Operario Dos',
            'email' => 'operario2@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('produccion');

        // // Propietario
        // \App\Models\User::factory()->create([
        //     'name' => 'Propietario Uno',
        //     'email' => 'propietario1@example.com',
        //     'password' => bcrypt('password'),
        // ])->assignRole('propietario');
    }
}

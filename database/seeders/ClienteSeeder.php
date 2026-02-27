<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca los usuarios con el rol 'cliente'
        $clientesUsers = User::role('cliente')->get();

        foreach ($clientesUsers as $user) {
            Cliente::create([
                'nit' => 'CF-' . $user->id,
                'razon_social' => $user->name,
                'user_id' => $user->id,
            ]);
        }
    }
}
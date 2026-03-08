<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $productionRole = DB::table('roles')->where('name', 'produccion')->first();
        if (!$productionRole) return;

        $users = DB::table('model_has_roles')
            ->where('role_id', $productionRole->id)
            ->where('model_type', 'App\Models\User')
            ->pluck('model_id');

        foreach ($users as $userId) {
            $exists = DB::table('operarios')->where('user_id', $userId)->exists();
            if (!$exists) {
                DB::table('operarios')->insert([
                    'user_id' => $userId,
                    'turno' => 'Mañana',
                    'especialidad' => 'General',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

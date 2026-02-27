<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AbonoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Abono::create([
            'fecha' => now(),
            'monto' => 50.00,
            'cuenta_cobro_id' => 1,
        ]);

        \App\Models\Abono::create([
            'fecha' => now()->addDays(10),
            'monto' => 100.00,
            'cuenta_cobro_id' => 2,
        ]);
        
    }
}

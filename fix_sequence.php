<?php
// fix_sequence.php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement("SELECT setval(pg_get_serial_sequence('orden_produccions', 'id'), coalesce(max(id), 1), max(id) IS NOT null) FROM orden_produccions;");
    DB::statement("SELECT setval(pg_get_serial_sequence('consumo_insumos', 'id'), coalesce(max(id), 1), max(id) IS NOT null) FROM consumo_insumos;");
    DB::statement("SELECT setval(pg_get_serial_sequence('movimiento_insumos', 'id'), coalesce(max(id), 1), max(id) IS NOT null) FROM movimiento_insumos;");
    DB::statement("SELECT setval(pg_get_serial_sequence('producto_terminados', 'id'), coalesce(max(id), 1), max(id) IS NOT null) FROM producto_terminados;");
    DB::statement("SELECT setval(pg_get_serial_sequence('movimiento_productos', 'id'), coalesce(max(id), 1), max(id) IS NOT null) FROM movimiento_productos;");
    
    echo "Secuencias sincronizadas correctamente.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = Illuminate\Support\Facades\DB::select("SELECT column_name, data_type, character_maximum_length 
                       FROM information_schema.columns 
                       WHERE table_name = 'pagos'
                       ORDER BY column_name");

$output = "";
foreach ($columns as $column) {
    $output .= "COL: {$column->column_name} | MAX: " . ($column->character_maximum_length ?? 'NONE') . " | TYPE: {$column->data_type}\n";
}
file_put_contents('pagos_columns.txt', $output);
echo "DONE\n";

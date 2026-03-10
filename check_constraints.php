<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$constraints = Illuminate\Support\Facades\DB::select("
    SELECT conname, pg_get_constraintdef(oid) as def
    FROM pg_constraint
    WHERE conrelid = 'pagos'::regclass;
");

foreach ($constraints as $con) {
    echo "Constraint: {$con->conname} | Def: {$con->def}\n";
}

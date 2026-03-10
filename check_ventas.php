<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'ventas' AND table_schema = 'public'");
foreach ($columns as $col) {
    echo "COL: {$col->column_name} | TYPE: {$col->data_type}\n";
}

$constraints = DB::select("SELECT conname, pg_get_constraintdef(oid) FROM pg_constraint WHERE conrelid = 'ventas'::regclass");
foreach ($constraints as $con) {
    echo "Constraint: {$con->conname} | Def: {$con->pg_get_constraintdef}\n";
}

<?php
include 'vendor/autoload.php';
$app = include 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$res = DB::select("SELECT pg_get_constraintdef(oid) as def FROM pg_constraint WHERE conrelid = 'ventas'::regclass AND conname LIKE '%tipo_pago%'");
echo "CONSTRAINT_DEF: " . ($res[0]->def ?? 'None') . "\n";

$res2 = DB::select("SELECT pg_get_constraintdef(oid) as def FROM pg_constraint WHERE conrelid = 'ventas'::regclass AND conname LIKE '%modo_pago%'");
echo "MODO_PAGO_DEF: " . ($res2[0]->def ?? 'None') . "\n";

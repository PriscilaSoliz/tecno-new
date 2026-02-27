<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaCuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'venta_id',
        'monto',
        'fecha_vencimiento',
        'fecha_pago',
        'estado',
        'numero_cuota',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}

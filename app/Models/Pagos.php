<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    protected $fillable = [
        'fecha',
        'monto',
        'metodo_pago',
        'venta_id',
        'referencia_externa',
        'transaction_id',
        'estado',
        'fecha_pago',
        'datos_pago',
    ];

    protected $casts = [
        'datos_pago' => 'array',
        'fecha' => 'datetime',
        'fecha_pago' => 'datetime',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaCobro extends Model
{
    protected $fillable = [
        'saldo_pendiente',
        'fecha_vencimiento',
        'venta_id',
        'cliente_id',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
}

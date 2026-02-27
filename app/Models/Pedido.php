<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'fecha',
        'total',
        'estado_produccion',
        'cliente_id',
        'direccion_entrega',
        'notas',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(PedidoDetalle::class);
    }

    public function rastreos()
    {
        return $this->hasMany(PedidoRastreo::class);
    }

    public function venta()
    {
        return $this->hasOne(Venta::class);
    }
}

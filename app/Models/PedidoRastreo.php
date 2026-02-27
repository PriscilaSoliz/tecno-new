<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoRastreo extends Model
{
    protected $fillable = [
        'latitud',
        'longitud',
        'hora',
        'pedido_id',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}

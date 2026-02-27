<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoPromocion extends Model
{
    protected $fillable = [
        'pedido_id',
        'promocion_id',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function promocion()
    {
        return $this->belongsTo(Promocion::class);
    }
    
}

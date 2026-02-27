<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoProductoBase extends Model
{
    protected $fillable = [
        'cantidad',
        'costo_final',
        'pedido_id',
        'producto_base_id',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function productoBase()
    {
        return $this->belongsTo(ProductoBase::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoTerminado extends Model
{
    protected $fillable = [
        'fecha_produccion',
        'cantidad_producida',
        'orden_produccion_id',
        'producto_id',
    ];

    public function ordenProduccion()
    {
        return $this->belongsTo(OrdenProduccion::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoProducto extends Model
{
    protected $fillable = [
        'producto_id',
        'orden_produccion_id',
        'tipo_movimiento',
        'cantidad',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function ordenProduccion()
    {
        return $this->belongsTo(OrdenProduccion::class);
    }
}

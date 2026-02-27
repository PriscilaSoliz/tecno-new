<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenProduccion extends Model
{
    protected $fillable = [
        'fecha_creacion',
        'cantidad_a_producir',
        'estado',
        'operario_id',
        'producto_id',
        'is_active',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    public function operario()
    {
        return $this->belongsTo(Operario::class);
    }
    public function productosTerminados()
    {
        return $this->hasMany(ProductoTerminado::class);
    }
    public function consumoInsumos()
    {
        return $this->hasMany(ConsumoInsumo::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteInsumo extends Model
{
    protected $fillable = [
        'fecha_ingreso',
        'cantidad_total_x_unidad',
        'cantidad_disponible_x_unidad',
        'costo_unitario',
        'costo_lote',
        'proveedor_id',
        'ingrediente_id',
    ];

    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class);
    }
    public function movimientos()
    {
        return $this->hasMany(MovimientoInsumo::class);
    }
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumoInsumo extends Model
{
    protected $fillable = [
        'cantidad_consumida',
        'orden_produccion_id',
        'ingrediente_id',
        'lote_insumo_id',
    ];

    public function ordenProduccion()
    {
        return $this->belongsTo(OrdenProduccion::class);
    }

    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class);
    }

    public function loteInsumo()
    {
        return $this->belongsTo(LoteInsumo::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInsumo extends Model
{
    protected $fillable = [
        'fecha',
        'cantidad',
        'tipo_movimiento',
        'motivo_movimiento',
        'ingrediente_id',
        'lote_insumo_id',
    ];
    // tipo movimiento
    // entrada | salida
    // motivo
    // entrada: compra | ajuste | devolucion salida: produccion | merma | ajuste

    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class);
    }

    public function loteInsumo()
    {
        return $this->belongsTo(LoteInsumo::class);
    }

}

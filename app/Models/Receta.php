<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    protected $fillable = [
        'cant_x_unidad',
        'producto_id',
        'ingrediente_id',
    ];

    protected $casts = [
        'cant_x_unidad' => 'decimal:2',
    ];

    // Alias para compatibilidad con el controlador (cantidad = cant_x_unidad)
    public function getCantidadAttribute()
    {
        return $this->cant_x_unidad;
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function ingrediente()
    {
        return $this->belongsTo(Ingrediente::class, 'ingrediente_id');
    }

}

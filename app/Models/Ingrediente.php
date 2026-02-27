<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingrediente extends Model
{
    protected $fillable = [
        'nombre',
        'unidad_medida',
        'descripcion',
        'is_active',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function lotes()
    {
        return $this->hasMany(LoteInsumo::class);
    }
    public function recetas()
    {
        return $this->hasMany(Receta::class);
    }
}

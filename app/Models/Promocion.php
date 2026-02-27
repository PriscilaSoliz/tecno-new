<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $fillable = [
        'producto_id',
        'precio',
        'fecha_inicio',
        'fecha_fin',
        'is_active',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    protected $hidden = [
        'created_at',
        'updated_at',
    ];


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $fillable = [
        'empresa',
        'contacto',
        'estado',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
}

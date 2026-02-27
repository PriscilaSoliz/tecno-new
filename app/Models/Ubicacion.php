<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $fillable = [
        'latitud',
        'longitud',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

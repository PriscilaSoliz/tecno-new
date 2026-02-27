<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operario extends Model
{
    protected $table = 'operarios';

    protected $fillable = [
        'turno',
        'especialidad',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

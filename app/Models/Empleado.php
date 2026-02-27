<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{

    protected $fillable = [
        'area',
        'cargo',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

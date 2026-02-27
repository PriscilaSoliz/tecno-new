<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'user_id',
        'nit',
        'razon_social',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}

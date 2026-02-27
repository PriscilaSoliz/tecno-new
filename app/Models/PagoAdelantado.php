<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoAdelantado extends Model
{
    protected $fillable = [
        'fecha',
        'pedido_id',
    ];

    public static function crearPagoAdelantado(int $pedido_id)
    {
        return self::create([
            'fecha' => now(),
            'pedido_id' => $pedido_id,
        ]);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
    

}

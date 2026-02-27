<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'fecha',
        'total',
        'tipo_pago',
        'modo_pago',
        'pedido_id',
        'cliente_id',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'detalle_ventas')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal');
    }

    public function pagos()
    {
        return $this->hasMany(Pagos::class);
    }

    public function cuentaCobro()
    {
        return $this->hasOne(CuentaCobro::class);
    }
    public function cuotas()
    {
        return $this->hasMany(VentaCuota::class);
    }
}

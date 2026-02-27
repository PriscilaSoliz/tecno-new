<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    protected $fillable = [
        'fecha',
        'monto',
        'cuenta_cobro_id',
    ];

    public function cuentaCobro()
    {
        return $this->belongsTo(CuentaCobro::class);
    }

}

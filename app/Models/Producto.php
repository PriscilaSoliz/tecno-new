<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'unidad_medida',
        'precio_venta',
        'descripcion',
        'imagen',
        'is_active',
        'has_promo',
    ];
    public function promocion()
    {
        return $this->hasOne(Promocion::class);
    }
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class);
    }
    public function movimientosProducto()
    {
        return $this->hasMany(MovimientoProducto::class);
    }
    /**
     * Relación con recetas (cada receta es un ingrediente con su cantidad)
     * Cada registro de 'recetas' representa un ingrediente necesario para este producto
     */
    public function recetas()
    {
        return $this->hasMany(Receta::class, 'producto_id');
    }

    public function productosTerminados()
    {
        return $this->hasMany(ProductoTerminado::class);
    }

    public function ordenesProduccion()
    {
        return $this->belongsToMany(OrdenProduccion::class);
    }
}

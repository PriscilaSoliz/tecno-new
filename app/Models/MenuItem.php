<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'title',
        'route',
        'icon',
        'parent_id',
        'order',
        'is_active',
        'roles',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        // No usamos cast 'array' aquí porque lo manejamos manualmente en el middleware
    ];

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    // Accessor para obtener roles como array
    public function getRolesArrayAttribute(): array
    {
        if (is_array($this->roles)) {
            return $this->roles;
        }
        if (is_string($this->roles)) {
            return json_decode($this->roles, true) ?? [];
        }
        return [];
    }
}

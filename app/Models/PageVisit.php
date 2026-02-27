<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class PageVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_name',
        'page_title',
        'page_path',
        'visit_count',
        'user_id'
    ];

    protected $casts = [
        'visit_count' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * Relación con el usuario
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registrar o actualizar una visita
     * Corregido para PostgreSQL
     */
    public static function recordVisit($pageName, $pageTitle, $pagePath, $userId = null)
    {
        // Buscar registro existente
        $visit = static::where('page_name', $pageName)
                      ->where('user_id', $userId)
                      ->first();

        if ($visit) {
            // Si existe, incrementar el contador
            $visit->increment('visit_count');
            $visit->update([
                'page_title' => $pageTitle,
                'page_path' => $pagePath,
            ]);
            return $visit;
        } else {
            // Si no existe, crear nuevo registro
            return static::create([
                'page_name' => $pageName,
                'page_title' => $pageTitle,
                'page_path' => $pagePath,
                'visit_count' => 1,
                'user_id' => $userId,
            ]);
        }
    }

    /**
     * Obtener visitas por página
     */
    public static function getVisitsByPage($pageName, $userId = null)
    {
        $query = static::where('page_name', $pageName);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->first()?->visit_count ?? 0;
    }

    /**
     * Reiniciar contador de todas las páginas
     */
    public static function resetAllVisits($userId = null)
    {
        $query = static::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->delete();
    }

    /**
     * Reiniciar contador de una página específica
     */
    public static function resetPageVisits($pageName, $userId = null)
    {
        $query = static::where('page_name', $pageName);
        
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->delete();
    }

    /**
     * Obtener todas las visitas del usuario con estadísticas
     */
    public static function getUserStatistics($userId)
    {
        return static::where('user_id', $userId)
                    ->select('page_name', 'page_title', 'visit_count', 'page_path')
                    ->orderBy('visit_count', 'desc')
                    ->get();
    }
}

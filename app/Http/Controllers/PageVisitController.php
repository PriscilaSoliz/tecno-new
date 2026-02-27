<?php

namespace App\Http\Controllers;

use App\Models\PageVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageVisitController extends Controller
{
    /**
     * Obtener visitas de la página actual
     */
    public function show(Request $request)
    {
        $route = \Route::current();
        $pageName = $route ? $route->getName() : 'unknown';

        $visits = PageVisit::getVisitsByPage($pageName, Auth::id());

        return response()->json([
            'page_name' => $pageName,
            'visits' => $visits,
        ]);
    }

    /**
     * Obtener todas las visitas del usuario
     */
    public function getAllVisits(Request $request)
    {
        $visits = PageVisit::where('user_id', Auth::id())
            ->orderBy('visit_count', 'desc')
            ->get();

        return response()->json($visits);
    }

    /**
     * Reiniciar contador de todas las páginas
     */
    public function resetAll(Request $request)
    {
        PageVisit::resetAllVisits(Auth::id());

        // Redirigir de vuelta con mensaje de éxito
        return redirect()->back()->with('success', 'Todos los contadores han sido reiniciados');
    }

    /**
     * Reiniciar contador de una página específica
     */
    public function reset(Request $request, $pageName)
    {
        PageVisit::resetPageVisits($pageName, Auth::id());

        return redirect()->back()->with('success', 'Contador reiniciado correctamente');
    }
}

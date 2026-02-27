<?php

namespace App\Http\Controllers\Produccion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;


class ProduccionController extends Controller
{
    // entra
    // - productos
    
    public function index()
    {
        return Inertia::render('Produccion/Index');
    }
}

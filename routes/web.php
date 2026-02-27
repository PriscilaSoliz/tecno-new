<?php

use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\PromocionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IngredienteController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\PageVisitController;
use App\Http\Controllers\VentaController;
use App\Models\Inventario;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventario\MetodoController;
use App\Http\Controllers\Produccion\ProduccionController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\PedidoController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard con redirección según rol
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Cliente -> redirigir a catálogo
        if ($user->hasRole('cliente')) {
            return redirect()->route('catalogo.index');
        }

        // Encargado de almacén -> redirigir a inventario
        if ($user->hasRole('encargadoalmacen')) {
            return redirect()->route('inventory');
        }

        // Producción -> mostrar dashboard de producción o redirigir
        if ($user->hasRole('produccion')) {
            return redirect()->route('production');
        }

        // Propietario y otros -> mostrar dashboard normal
        return Inertia::render('Dashboard1');
    })->name('dashboard');

    Route::get('/menu', [Controller::class, 'index'])->name('menu.index');

    // Ruta de Accesibilidad
    Route::get('/accesibilidad', function () {
        return Inertia::render('Accesibilidad/index');
    })->name('accesibilidad.index');

    Route::prefix('pedidos')->group(function () {
        // Propietario/gestión general
        Route::get('/', [PedidoController::class, 'index'])->name('pedidos.index');
        Route::post('/', [PedidoController::class, 'store'])->name('pedidos.store');
        Route::get('/{id}', [PedidoController::class, 'show'])->name('pedidos.show');
        Route::put('/{id}', [PedidoController::class, 'update'])->name('pedidos.update');
        Route::delete('/{id}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');
        // Acción rápida: crear venta sin configuración (botón en index.vue)
        Route::post('/{id}/quick-complete', [PedidoController::class, 'quickComplete'])->name('pedidos.quick-complete');
    });

    // Rutas de Cliente (Mis Pedidos) - Usando PedidoController
    Route::prefix('mis-pedidos')->group(function () {
        Route::get('/', [PedidoController::class, 'clienteIndex'])->name('cliente.pedidos.index');
        Route::get('/{id}', [PedidoController::class, 'clienteShow'])->name('cliente.pedidos.show');
        Route::put('/{id}/recibido', [PedidoController::class, 'clienteRecibido'])->name('cliente.pedidos.recibido');
        // Pagar cuota
        Route::put('/cuota/{id}/pagar', [PedidoController::class, 'pagarCuota'])->name('pedidos.cuota.pagar');
    });

    Route::prefix('inventario')->group(function () {
        Route::get('/', [InventarioController::class, 'index'])->name('inventory');

        // PROVEEDOR
        Route::prefix('proveedores')->group(function () {
            Route::get('/', [ProveedorController::class, 'index'])->name('proveedores.index');
            Route::post('/', [ProveedorController::class, 'store'])->name('proveedores.store');
            Route::put('/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
        });
        // ALMACEN
        Route::prefix('almacen')->group(function () {
            // INGREDIENTES
            Route::get('/', [InventarioController::class, 'indexAlmacen'])->name('almacen.index');
            Route::post('/', [IngredienteController::class, 'store'])->name('ingredientes.store');
            Route::put('/{id}', [InventarioController::class, 'updateIngrediente'])->name('almacen.ingredientes.update');
            Route::delete('/{id}', [InventarioController::class, 'destroyIngrediente'])->name('ingredientes.destroy');
            // ENTRADA
            Route::prefix('entrada')->group(function () {
                Route::get('/{idIngrediente}', [InventarioController::class, 'entradaCreate'])->name('almacen.ingredientes.entrada.create');
                Route::post('/', [InventarioController::class, 'entradaStore'])->name('entrada.store');
            });
            // SALIDA
            Route::prefix('salida')->group(function () {
                Route::get('/{idIngrediente}', [InventarioController::class, 'salidaCreate'])->name('almacen.ingredientes.salida.create');
                Route::post('/', [InventarioController::class, 'salidaStore'])->name('almacen.ingredientes.salida.store');
            });
        });
    });


    Route::prefix('produccion')->group(function () {
        Route::get('/', [ProduccionController::class, 'index'])->name('production');
        Route::post('/', [ProductoController::class, 'store'])->name('productos.store');
        Route::put('/{id}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');


        // PRODUCTO
        Route::prefix('productos')->group(function () {
            Route::get('/', [ProductoController::class, 'index'])->name('productos.index');
            Route::get('/{id}', [ProductoController::class, 'show'])->name('productos.show');
            Route::post('/', [ProductoController::class, 'store'])->name('productos.store');
            Route::put('/{id}', [ProductoController::class, 'update'])->name('productos.update');
            Route::delete('/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        });
        // RECETAS
        Route::prefix('recetas')->group(function () {
            Route::get('/', [RecetaController::class, 'index'])->name('recetas.index');
            Route::get('/create', [RecetaController::class, 'create'])->name('recetas.create');
            Route::post('/', [RecetaController::class, 'store'])->name('recetas.store');
            Route::put('/{id}', [RecetaController::class, 'update'])->name('recetas.update');
            Route::delete('/{id}', [RecetaController::class, 'destroy'])->name('recetas.destroy');
        });
        // ORDENES DE PRODUCCION
        Route::prefix('ordenes')->group(function () {
            Route::get('/', [OrdenController::class, 'index'])->name('ordenes.index');
            Route::post('/', [OrdenController::class, 'store'])->name('ordenes.store');
            Route::put('/{id}', [OrdenController::class, 'update'])->name('ordenes.update'); // Maneja action=ejecutar y action=finalizar
            Route::delete('/{id}', [OrdenController::class, 'destroy'])->name('ordenes.destroy');
        });
    });
    Route::prefix('Contabilidad')->group(function () {
        // METODO DE INVENTARIO
        Route::prefix('metodos')->group(function () {
            Route::get('/', [MetodoController::class, 'index'])->name('metodos.index');
            Route::get('/peps', [MetodoController::class, 'indexPeps'])->name('inventario.metodo.peps');
            Route::get('/ueps', [MetodoController::class, 'indexUeps'])->name('inventario.metodo.ueps');
            Route::get('/promedio-ponderado', [MetodoController::class, 'indexPromedioPonderado'])->name('inventario.metodo.promedio');
        });
    });
    // CRUD de MenuItems (protegido por auth)
    Route::middleware(['auth'])->group(function () {
        Route::resource('menu', MenuItemController::class);
        Route::resource('promociones', PromocionController::class)->only(['store', 'update', 'destroy']);
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
    });

    // Rutas de PageVisit
    Route::prefix('page-visits')->group(function () {
        Route::post('/reset-all', [PageVisitController::class, 'resetAll'])->name('page-visits.reset-all');
        Route::post('/reset/{pageName}', [PageVisitController::class, 'reset'])->name('page-visits.reset');
        Route::get('/all', [PageVisitController::class, 'getAllVisits'])->name('page-visits.all');
    });

    Route::prefix('api/dashboard')->group(function () {
        Route::get('/top-products', [VentaController::class, 'topProducts']);
        Route::get('/sales-timeline', [VentaController::class, 'salesTimeline']);
    });

    // Rutas de PagoFácil - generar QR (autenticada)
    Route::prefix('pagofacil')->group(function () {
        Route::post('/generar-qr', [\App\Http\Controllers\PagoFacilController::class, 'generarQR'])->name('pagofacil.generar-qr');
    });

    // Buscador Global
    Route::get('/global-search', [\App\Http\Controllers\GlobalSearchController::class, 'search'])->name('global.search');

    // Stripe
    Route::prefix('stripe')->group(function () {
        Route::post('/payment-intent', [\App\Http\Controllers\StripeController::class, 'createPaymentIntent'])->name('stripe.payment-intent');
    });
});

// Catálogo público (nombre requerido para redirects como route('catalogo.index'))
Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');

// Catálogo - procesar venta (requiere autenticación pero permite todos los roles)
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/catalogo/venta', [CatalogoController::class, 'venta'])->name('catalogo.venta.get');
    Route::post('/catalogo/venta', [CatalogoController::class, 'venta'])->name('catalogo.venta');
    Route::post('/catalogo/confirmar', [CatalogoController::class, 'confirmar'])->name('catalogo.confirmar');
});





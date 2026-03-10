<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::select(['id', 'nombre', 'unidad_medida', 'precio_venta', 'descripcion', 'imagen', 'is_active']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'LIKE', "%{$search}%");
        }

        $productos = $query->with('promocion')->get();

        return Inertia::render('Produccion/Productos/Index', [
            'productos' => $productos,
            'filters' => $request->only(['search']),
        ]);
    }

    public function show($id)
    {
        $producto = Producto::findOrFail($id);

        return Inertia::render('Produccion/Productos/Show', [
            'producto' => $producto,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:255',
            'precio_venta' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'is_active' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imagenPath = null;

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $path = public_path('images/products');
            
            // Asegurar que el directorio de productos y su padre existan con permisos amplios
            if (!File::exists(public_path('images'))) {
                File::makeDirectory(public_path('images'), 0777, true, true);
            }
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Forzar permisos de escritura
            @chmod(public_path('images'), 0777);
            @chmod($path, 0777);

            try {
                // Intento estándar
                $file->move($path, $filename);
            } catch (\Exception $e) {
                // Fallback manual: útil en servidores donde move_uploaded_file está restringido
                $fullPath = $path . DIRECTORY_SEPARATOR . $filename;
                if (file_put_contents($fullPath, file_get_contents($file->getRealPath())) === false) {
                    return back()->withErrors(['imagen' => 'No se pudo escribir en el servidor. Por favor asigne permisos 777 a public/images/products/']);
                }
            }
            $imagenPath = '/images/products/' . $filename;
        }

        $producto = Producto::create([
            'nombre' => $request->nombre,
            'unidad_medida' => $request->unidad_medida,
            'precio_venta' => $request->precio_venta,
            'descripcion' => $request->descripcion,
            'imagen' => $imagenPath,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : true,
        ]);

        return redirect()->route('productos.index')
            ->with('success', 'Producto ' . $producto->nombre . ' creado exitosamente');
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'unidad_medida' => 'required|string|max:255',
            'precio_venta' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'is_active' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only(['nombre', 'unidad_medida', 'precio_venta', 'descripcion', 'is_active']);

        // Manejar eliminación de imagen
        if ($request->input('remove_image') === '1') {
            // Eliminar archivo anterior si existe
            if ($producto->imagen && file_exists(public_path($producto->imagen))) {
                @unlink(public_path($producto->imagen));
            }
            $data['imagen'] = null;
        }

        // Manejar nueva imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen && File::exists(public_path($producto->imagen))) {
                @unlink(public_path($producto->imagen));
            }

            $file = $request->file('imagen');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $path = public_path('images/products');
            
            // Asegurar directorios y permisos
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            @chmod(public_path('images'), 0777);
            @chmod($path, 0777);

            try {
                $file->move($path, $filename);
            } catch (\Exception $e) {
                // Fallback manual
                $fullPath = $path . DIRECTORY_SEPARATOR . $filename;
                if (file_put_contents($fullPath, file_get_contents($file->getRealPath())) === false) {
                    return back()->withErrors(['imagen' => 'Error de permisos al subir la imagen.']);
                }
            }
            $data['imagen'] = '/images/products/' . $filename;
        }

        $producto->update($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        // Eliminar imagen si existe
        if ($producto->imagen && File::exists(public_path($producto->imagen))) {
            @unlink(public_path($producto->imagen));
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente');
    }
}

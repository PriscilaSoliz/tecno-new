<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'nombre' => 'Pan Francés',
                'unidad_medida' => 'unidad',
                'precio_venta' => 1.50,
                'descripcion' => 'Pan francés clásico, corteza crujiente y miga suave.',
                'imagen' => '/images/products/pan1.jpg',
                'is_active' => true
            ],
            [
                'nombre' => 'Baguette Rústica',
                'unidad_medida' => 'unidad',
                'precio_venta' => 2.20,
                'descripcion' => 'Baguette larga, corteza dorada y miga aireada.',
                'imagen' => '/images/products/pan2.jpg',
                'is_active' => true
            ],
            [
                'nombre' => 'Pan de Molde Blanco',
                'unidad_medida' => 'unidad',
                'precio_venta' => 12.00,
                'descripcion' => 'Pan de molde blanco, ideal para sándwiches (barra grande).',
                'imagen' => '/images/products/pan3.jpg',
                'is_active' => true
            ],
            [
                'nombre' => 'Pan Integral con Semillas',
                'unidad_medida' => 'unidad',
                'precio_venta' => 3.50,
                'descripcion' => 'Pan integral artesanal con mezcla de semillas saludables.',
                'imagen' => '/images/products/pan4.jpg',
                'is_active' => true
            ],
            [
                'nombre' => 'Croissant de Mantequilla',
                'unidad_medida' => 'unidad',
                'precio_venta' => 2.50,
                'descripcion' => 'Croissant francés hojaldrado, elaborado con mantequilla real.',
                'imagen' => '/images/products/pan5.jpg',
                'is_active' => true
            ],
            [
                'nombre' => 'Pan de Avena y Miel',
                'unidad_medida' => 'unidad',
                'precio_venta' => 3.00,
                'descripcion' => 'Pan suave con avena y un toque de miel natural.',
                'imagen' => '/images/products/pan6.jpg',
                'is_active' => true
            ],
            [
                'nombre' => 'Pan de Centeno',
                'unidad_medida' => 'unidad',
                'precio_venta' => 3.20,
                'descripcion' => 'Pan denso de centeno con aroma profundo y corteza firme.',
                'imagen' => '/images/products/pan7.jpg',
                'is_active' => true
            ],
            [
                'nombre' => 'Pan Multigrano',
                'unidad_medida' => 'unidad',
                'precio_venta' => 3.80,
                'descripcion' => 'Pan con mezcla de granos y semillas tostadas, rico en fibra.',
                'imagen' => '/images/products/pan8.jpg',
                'is_active' => true
            ],
        ];

        foreach ($items as $it) {
            Producto::updateOrCreate(
                ['nombre' => $it['nombre']],
                $it
            );
        }
    }
}

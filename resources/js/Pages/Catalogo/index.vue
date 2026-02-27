<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Carrito from './Components/Carrito.vue';

const props = defineProps({
    productos: { type: Array, default: () => [] }
});

const searchQuery = ref('');
const showCarrito = ref(false);
const carrito = ref([]);

// Detectar parámetro de búsqueda desde URL (cuando viene del buscador global)
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('search');
    if (searchParam) {
        searchQuery.value = searchParam;
    }
});

// Usuario actual
const page = usePage();
const usuario = computed(() => page.props.auth?.user || null);

// Filtrado de productos
const productosFiltrados = computed(() => {
    const q = searchQuery.value.toLowerCase().trim();
    if (!q) return props.productos;
    return props.productos.filter(p =>
        p.nombre.toLowerCase().includes(q) ||
        (p.descripcion || '').toLowerCase().includes(q)
    );
});

// Agregar al carrito
const agregarAlCarrito = (producto) => {
    const existe = carrito.value.find(item => item.id === producto.id);
    // Calcular precio final (promo o venta)
    const precioFinal = producto.promocion ? Number(producto.promocion.precio) : Number(producto.precio_venta);

    if (existe) {
        existe.cantidad++;
    } else {
        carrito.value.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: precioFinal,
            imagen: producto.imagen,
            cantidad: 1
        });
    }
    showCarrito.value = true; // Abrir el carrito automáticamente al agregar
};

// Manejo de errores de imagen para evitar parpadeo
const handleImageError = (e) => {
    // Solo reemplazar si no es ya la imagen por defecto para evitar bucle
    if (!e.target.src.includes('placeholder')) {
        e.target.src = 'https://placehold.co/400x300/EEE/31343C?text=Pan+de+Casa';
    }
};

// Helper: si ya empieza con / o http, usarlo directo; si no, agregar /
const getImageUrl = (img) => {
    if (!img) return 'https://placehold.co/400x300/EEE/31343C?text=Producto';
    if (typeof img !== 'string') return 'https://placehold.co/400x300/EEE/31343C?text=Producto';
    // Si ya es absoluta (empieza con / o http), usarla directamente
    if (img.startsWith('http') || img.startsWith('/')) return img;
    // Caso legacy: agregar / al inicio
    return `/${img}`;
};

// Total de items en carrito
const totalItems = computed(() => carrito.value.reduce((sum, item) => sum + item.cantidad, 0));

// Ir a página de detalle de venta
const irAVenta = () => {
    if (carrito.value.length === 0) {
        alert('El carrito está vacío');
        return;
    }

    // Guardar backup en localStorage ANTES de navegar
    localStorage.setItem('carrito_backup', JSON.stringify(carrito.value));

    showCarrito.value = false;

    // Calcular total
    const total = carrito.value.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);

    console.log('🛒 Enviando a venta:', { productos: carrito.value, total });

    router.post(route('catalogo.venta'), {
        productos: carrito.value,
        total: total,
        cliente_id: usuario.value?.id || null
    });
};
</script>

<template>
    <AppLayout title="Catálogo de Productos">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800">Catálogo de Productos</h2>
                <!-- Botón Carrito flotante -->
                <button @click="showCarrito = !showCarrito"
                    class="relative bg-amber-600 text-white px-4 py-2 rounded-full shadow-lg hover:bg-amber-700 transition flex items-center gap-2 z-10">
                    <i class="fas fa-shopping-basket text-lg"></i>
                    <span class="font-medium hidden sm:inline">Mi Canasta</span>
                    <span v-if="totalItems > 0"
                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-6 h-6 rounded-full flex items-center justify-center font-bold border-2 border-white animate-pulse">
                        {{ totalItems }}
                    </span>
                </button>
            </div>
        </template>

        <div class="py-8 bg-gradient-to-br from-amber-50 via-white to-emerald-50 min-h-screen relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Buscador -->
                <div class="mb-8">
                    <div class="relative max-w-2xl mx-auto">
                        <input v-model="searchQuery" type="text"
                            placeholder="¿Qué se te antoja hoy? (Baguette, Croissant...)"
                            class="w-full pl-12 pr-4 py-4 border-2 border-amber-200 rounded-full shadow-sm focus:ring-4 focus:ring-amber-100 focus:border-amber-400 text-lg transition-all" />
                        <i class="fas fa-search absolute left-5 top-5 text-amber-400 text-xl"></i>
                    </div>
                </div>

                <!-- Grid de Productos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div v-for="producto in productosFiltrados" :key="producto.id"
                        class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-amber-300 flex flex-col h-full">
                        <!-- Imagen del Producto (Reducida) -->
                        <div class="relative h-48 bg-gray-100 overflow-hidden">
                            <img :src="getImageUrl(producto.imagen)" :alt="producto.nombre"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                @error="handleImageError" />
                            <div
                                class="absolute top-2 right-2 bg-white/90 backdrop-blur text-emerald-600 text-xs px-2 py-1 rounded-md font-bold shadow-sm">
                                Disponible
                            </div>
                            <!-- Badge OFERTA (Top Left) -->
                            <div v-if="producto.promocion"
                                class="absolute top-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded-md font-bold shadow-sm animate-pulse">
                                ¡OFERTA!
                            </div>
                        </div>

                        <!-- Información del Producto (Más Compacta) -->
                        <div class="p-3 flex-1 flex flex-col">
                            <h3
                                class="text-base font-bold text-gray-800 mb-1 line-clamp-2 group-hover:text-amber-700 transition">
                                {{ producto.nombre }}
                            </h3>
                            <p class="text-xs text-gray-500 mb-3 line-clamp-2 flex-1">
                                {{ producto.descripcion || 'Delicioso producto artesanal.' }}
                            </p>

                            <!-- Precio y Botón (Ajustado) -->
                            <div class="flex items-end justify-between mt-auto pt-2 border-t border-gray-50">
                                <div>
                                    <p class="text-xs text-gray-400 mb-0.5">Precio</p>

                                    <div v-if="producto.promocion">
                                        <p class="text-xs text-gray-400 line-through">Bs{{
                                            Number(producto.precio_venta).toFixed(2) }}</p>
                                        <p class="text-xl font-extrabold text-red-600">
                                            Bs{{ Number(producto.promocion.precio).toFixed(2) }}
                                        </p>
                                    </div>
                                    <p v-else class="text-lg font-extrabold text-gray-900">
                                        Bs{{ Number(producto.precio_venta).toFixed(2) }}
                                    </p>
                                </div>
                                <button @click="agregarAlCarrito(producto)"
                                    class="bg-amber-100 text-amber-700 hover:bg-amber-600 hover:text-white px-3 py-1.5 rounded-lg transition-all duration-200 font-medium flex items-center gap-1 shadow-sm hover:shadow-md">
                                    <i class="fas fa-plus text-sm"></i>
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mensaje cuando no hay productos -->
                <div v-if="productosFiltrados.length === 0" class="text-center py-16">
                    <div class="inline-block p-6 bg-amber-50 rounded-full mb-4">
                        <i class="fas fa-cookie-bite text-4xl text-amber-300"></i>
                    </div>
                    <p class="text-xl text-gray-600 font-medium">No encontramos lo que buscas</p>
                    <p class="text-gray-400">Intenta con otro término</p>
                </div>
            </div>

            <!-- Componente Carrito (Modal Flotante) -->
            <!-- Teleportamos al body para evitar problemas de z-index con contenedores padres -->
            <Teleport to="body">
                <Carrito v-if="showCarrito" :items="carrito" @close="showCarrito = false" @checkout="irAVenta" />
            </Teleport>
        </div>
    </AppLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

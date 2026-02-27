<script setup>
import { computed } from 'vue';

const props = defineProps({
    items: { type: Array, default: () => [] }
});

const emit = defineEmits(['close', 'checkout']);

const total = computed(() => 
    props.items.reduce((sum, item) => sum + (item.precio * item.cantidad), 0)
);

const eliminarItem = (index) => {
    props.items.splice(index, 1);
};

const incrementar = (item) => {
    item.cantidad++;
};

const decrementar = (item) => {
    if (item.cantidad > 1) item.cantidad--;
};

const handleImageError = (e) => {
    e.target.src = 'https://placehold.co/100x100/EEE/31343C?text=IMG';
};
</script>

<template>
    <!-- Overlay -->
    <div @click="emit('close')" class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm"></div>

    <!-- Sidebar Carrito Estándar (como en e-commerce) -->
    <div class="fixed top-0 right-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col z-[200]">
        
        <!-- Header -->
        <div class="p-6 bg-gradient-to-r from-amber-600 to-orange-600 text-white flex justify-between items-center">
            <h3 class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-shopping-cart"></i> Carrito de Compras
            </h3>
            <button @click="emit('close')" class="text-white/80 hover:text-white transition-colors w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Lista de Items (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <div v-for="(item, idx) in items" :key="idx" 
                 class="flex gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm">
                
                <!-- Imagen -->
                <div class="w-20 h-20 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                    <img :src="item.imagen" 
                         class="w-full h-full object-cover" 
                         alt="Producto" 
                         @error="handleImageError"/>
                </div>

                <!-- Detalles del Item -->
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800 text-sm line-clamp-2">{{ item.nombre }}</h4>
                        <p class="text-amber-600 font-bold text-lg mt-1">Bs {{ Number(item.precio).toFixed(2) }}</p>
                    </div>
                    
                    <!-- Controles de Cantidad y Subtotal -->
                    <div class="flex justify-between items-center mt-2">
                        <!-- Controles de Cantidad -->
                        <div class="flex items-center bg-white rounded-lg border border-gray-200">
                            <button @click="decrementar(item)" 
                                    class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-l-lg text-gray-600 transition-colors">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span class="w-10 text-center text-sm font-bold text-gray-800">{{ item.cantidad }}</span>
                            <button @click="incrementar(item)" 
                                    class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-r-lg text-gray-600 transition-colors">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>

                        <!-- Subtotal -->
                        <div class="text-right">
                            <p class="text-sm text-gray-500">Subtotal</p>
                            <p class="font-bold text-gray-800">Bs {{ (item.precio * item.cantidad).toFixed(2) }}</p>
                        </div>

                        <!-- Eliminar -->
                        <button @click="eliminarItem(idx)" 
                                class="text-red-500 hover:text-red-700 transition-colors ml-2">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estado Vacío -->
            <div v-if="items.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-shopping-cart text-3xl text-gray-300"></i>
                </div>
                <p class="text-lg font-medium text-gray-500">Tu carrito está vacío</p>
                <p class="text-sm">¡Agrega algunos productos deliciosos!</p>
            </div>
        </div>

        <!-- Footer con Total y Checkout -->
        <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <span class="text-lg font-semibold text-gray-700">Total:</span>
                <span class="text-2xl font-extrabold text-amber-600">Bs {{ total.toFixed(2) }}</span>
            </div>
            <button 
                @click="emit('checkout')"
                :disabled="items.length === 0"
                class="w-full bg-gradient-to-r from-amber-600 to-orange-600 text-white py-3 rounded-lg font-bold text-lg hover:from-amber-700 hover:to-orange-700 disabled:from-gray-300 disabled:to-gray-400 disabled:cursor-not-allowed transition-all shadow-lg shadow-amber-200 flex items-center justify-center gap-2"
            >
                <span>Proceder al Pago</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

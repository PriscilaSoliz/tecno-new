<script setup>
import { computed } from 'vue';

const props = defineProps({
    productos: { type: Array, default: () => [] },
    total: { type: Number, default: 0 }
});

const handleImageError = (e) => {
    e.target.src = 'https://placehold.co/100x100/EEE/31343C?text=Pan+de+Casa';
};

// helper for product images
const getImageUrl = (img) => {
    if (!img) return 'https://placehold.co/100x100/EEE/31343C?text=Producto';
    if (typeof img !== 'string') return 'https://placehold.co/100x100/EEE/31343C?text=Producto';
    if (img.startsWith('http') || img.startsWith('/')) return img;
    return `/${img}`;
};

const subtotal = computed(() => props.total || 0);
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-shopping-bag text-amber-600"></i> Resumen del Pedido
            </h3>
        </div>
        
        <div class="divide-y divide-gray-100">
            <div v-for="(prod, idx) in productos" :key="idx" 
                 class="flex items-center gap-4 p-6 hover:bg-gray-50 transition-colors">
                <div class="w-20 h-20 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 shadow-sm">
                    <img :src="getImageUrl(prod.imagen)" 
                         class="w-full h-full object-cover" 
                         @error="handleImageError"/>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800 text-lg">{{ prod.nombre }}</h4>
                    <p class="text-sm text-gray-500">Cantidad: <span class="font-medium text-gray-700">{{ prod.cantidad }}</span></p>
                    <p class="text-sm text-gray-500">Precio unitario: Bs {{ Number(prod.precio || 0).toFixed(2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xl font-bold text-amber-600">Bs {{ (Number(prod.precio || 0) * Number(prod.cantidad || 1)).toFixed(2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Subtotal y Envío -->
        <div class="p-6 bg-gray-50 border-t border-gray-100">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-medium">Bs {{ subtotal.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Envío</span>
                    <span class="text-green-600 font-medium">Gratis</span>
                </div>
                <div class="flex justify-between font-bold text-lg pt-3 border-t border-gray-200 mt-2">
                    <span class="text-gray-800">Total</span>
                    <span class="text-amber-600 text-xl">Bs {{ subtotal.toFixed(2) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    carrito: Array,
    cliente_id: Number,
});

const page = usePage();
const cliente = computed(() => page.props.auth.user);
const total = computed(() => props.carrito.reduce((sum, item) => sum + item.precio * item.cantidad, 0));

const form = useForm({
    productos: props.carrito,
    tipo_pago: 'efectivo',
    modalidad_pago: 'contado',
    numero_cuotas: 3,
    notas: '',
});

const finalizarCompra = () => {
    if (!confirm('¿Confirmar compra?')) return;
    form.post(route('catalogo.confirmar'));
};

const montoPorCuota = computed(() => {
    if (form.modalidad_pago !== 'cuotas' || !total.value) return 0;
    return (total.value / form.numero_cuotas).toFixed(2);
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

const goBack = () => {
    window.history.back();
};
</script>

<template>
    <AppLayout title="Finalizar Compra">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-amber-600"></i> Finalizar Compra
                </h2>
                <button @click="goBack" class="text-sm text-gray-600 hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i> Volver al catálogo
                </button>
            </div>
        </template>

        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-6 border-b border-gray-100 bg-gray-50">
                                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-shopping-bag text-amber-600"></i> Resumen del Pedido
                                </h3>
                            </div>
                            <div class="divide-y divide-gray-100">
                                <div v-for="item in carrito" :key="item.id" class="flex items-center gap-4 p-6 hover:bg-gray-50 transition-colors">
                                    <div class="w-20 h-20 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                        <img :src="getImageUrl(item.imagen)" class="w-full h-full object-cover" @error="handleImageError"/>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800 text-lg">{{ item.nombre }}</h4>
                                        <p class="text-sm text-gray-500">Cantidad: {{ item.cantidad }}</p>
                                        <p class="text-sm text-gray-500">Precio unitario: Bs {{ item.precio.toFixed(2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-amber-600">Bs {{ (item.precio * item.cantidad).toFixed(2) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6 bg-gray-50 border-t border-gray-100">
                                <div class="space-y-2">
                                    <div class="flex justify-between font-bold text-lg pt-2">
                                        <span>Total</span>
                                        <span class="text-amber-600">Bs {{ total.toFixed(2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-user-circle text-blue-600"></i> Información del Cliente
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <span class="block text-gray-500 text-xs uppercase mb-1">Nombre</span>
                                    <span class="font-semibold text-gray-800">{{ cliente?.name || 'Invitado' }}</span>
                                </div>
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <span class="block text-gray-500 text-xs uppercase mb-1">Email</span>
                                    <span class="font-semibold text-gray-800">{{ cliente?.email || '-' }}</span>
                                </div>
                            </div>
                             <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <span class="block text-gray-500 text-xs uppercase mb-1">Dirección de Entrega</span>
                                <span class="font-semibold text-gray-800">Se usará tu ubicación guardada.</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-credit-card text-emerald-600"></i> Método de Pago
                            </h3>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Modalidad</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button @click="form.modalidad_pago = 'contado'"
                                            :class="form.modalidad_pago === 'contado' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700'"
                                            class="py-2 px-4 rounded-lg font-medium transition-colors">
                                        Al Contado
                                    </button>
                                    <button @click="form.modalidad_pago = 'cuotas'"
                                            :class="form.modalidad_pago === 'cuotas' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-700'"
                                            class="py-2 px-4 rounded-lg font-medium transition-colors">
                                        En Cuotas
                                    </button>
                                </div>
                            </div>

                            <div v-if="form.modalidad_pago === 'cuotas'" class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <label class="block text-sm font-medium text-blue-800 mb-2">Número de Cuotas</label>
                                <select v-model="form.numero_cuotas" class="w-full border-blue-300 rounded-lg focus:ring-blue-500">
                                    <option :value="3">3 cuotas de Bs{{ montoPorCuota }}</option>
                                    <option :value="6">6 cuotas de Bs{{ (total / 6).toFixed(2) }}</option>
                                    <option :value="12">12 cuotas de Bs{{ (total / 12).toFixed(2) }}</option>
                                </select>
                            </div>

                            <div class="space-y-3">
                                <label class="flex items-center gap-4 p-4 border rounded-xl cursor-pointer transition-all hover:shadow-sm"
                                       :class="form.tipo_pago === 'efectivo' ? 'border-emerald-500 bg-emerald-50 ring-1 ring-emerald-500' : 'border-gray-200'">
                                    <input type="radio" v-model="form.tipo_pago" value="efectivo" class="text-emerald-600" />
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-800">Efectivo</span>
                                            <p class="text-xs text-gray-500">Pago en efectivo al recibir</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-4 p-4 border rounded-xl cursor-pointer transition-all hover:shadow-sm"
                                       :class="form.tipo_pago === 'tarjeta' ? 'border-blue-500 bg-blue-50 ring-1 ring-blue-500' : 'border-gray-200'">
                                    <input type="radio" v-model="form.tipo_pago" value="tarjeta" class="text-blue-600" />
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-800">Tarjeta</span>
                                            <p class="text-xs text-gray-500">Crédito o débito</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-4 p-4 border rounded-xl cursor-pointer transition-all hover:shadow-sm"
                                       :class="form.tipo_pago === 'qr' ? 'border-purple-500 bg-purple-50 ring-1 ring-purple-500' : 'border-gray-200'">
                                    <input type="radio" v-model="form.tipo_pago" value="qr" class="text-purple-600" />
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                            <i class="fas fa-qrcode"></i>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-800">Código QR</span>
                                            <p class="text-xs text-gray-500">Pago móvil</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                            <div class="text-center mb-6">
                                <p class="text-sm text-gray-600 mb-1">Total a Pagar</p>
                                <p class="text-4xl font-extrabold text-amber-600">Bs {{ total.toFixed(2) }}</p>
                                <p v-if="form.modalidad_pago === 'cuotas'" class="text-sm text-gray-500 mt-1">
                                    {{ form.numero_cuotas }} cuotas de Bs {{ montoPorCuota }}
                                </p>
                            </div>
                            
                            <button @click="finalizarCompra" 
                                    class="w-full bg-gradient-to-r from-emerald-600 to-green-600 text-white py-4 rounded-xl font-bold text-lg hover:from-emerald-700 hover:to-green-700 transition-all shadow-lg shadow-emerald-200 flex items-center justify-center gap-2 transform active:scale-95"
                                    :disabled="form.processing">
                                <i class="fas fa-lock"></i>
                                <span>Confirmar Pedido</span>
                            </button>
                            
                            <p class="text-xs text-gray-500 text-center mt-4">
                                Al confirmar, aceptas nuestros términos y condiciones.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

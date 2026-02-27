<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import QRModal from '@/Pages/PagoFacil/QRModal.vue';
import axios from 'axios';

const props = defineProps({
    venta_id: { type: Number, required: true },
    total: { type: Number, required: true },
    productos: { type: Array, default: () => [] },
    cliente: { type: Object, default: () => ({}) },
    es_cuota: { type: Boolean, default: false } // Indica si es pago en cuotas
});

// Estado
const showQRModal = ref(true);
const loading = ref(false);
const paymentStatus = ref(null);
const pollingInterval = ref(null);

// Manejo de éxito del pago
const handleQRSuccess = (data) => {
    showQRModal.value = false;
    paymentStatus.value = 'completado';

    // Limpiar localStorage
    if (typeof localStorage !== 'undefined') {
        localStorage.removeItem('carrito_backup');
    }

    // Mostrar mensaje de éxito
    alert('¡Pago completado! Tu pedido ha sido procesado correctamente.');

    // Redirigir a mis pedidos
    setTimeout(() => {
        router.visit(route('cliente.pedidos.index'));
    }, 1000);
};

// Manejo de cierre del modal
const handleQRClose = () => {
    showQRModal.value = false;
    // El usuario puede cerrar y completar el pago después
};

// Volver al catálogo
const volverAlCatalogo = () => {
    router.visit(route('catalogo.index'));
};
</script>

<template>
    <AppLayout title="Pago con QR">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 flex items-center gap-2">
                    <i class="fas fa-qrcode text-purple-600"></i> Pago con Código QR
                </h2>
            </div>
        </template>

        <div class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Columna Izquierda - Resumen -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-purple-100">
                                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-shopping-bag text-purple-600"></i> Resumen del Pedido
                                </h3>
                            </div>

                            <div class="divide-y divide-gray-100">
                                <div v-for="item in productos" :key="item.id"
                                    class="flex items-center gap-4 p-6 hover:bg-gray-50 transition-colors">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800">{{ item.nombre }}</h4>
                                        <p class="text-sm text-gray-500">Cantidad: {{ item.cantidad }}</p>
                                        <p class="text-sm text-gray-500">Precio: Bs {{ item.precio.toFixed(2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-purple-600">Bs {{ (item.precio *
                                            item.cantidad).toFixed(2) }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 bg-gray-50 border-t border-gray-100">
                                <div v-if="es_cuota" class="mb-3 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                    <p class="text-sm text-purple-700 font-semibold">
                                        <i class="fas fa-info-circle mr-1"></i> Pago en Cuotas
                                    </p>
                                    <p class="text-xs text-purple-600 mt-1">
                                        Estás pagando la primera cuota
                                    </p>
                                </div>
                                <div class="flex justify-between font-bold text-xl">
                                    <span>{{ es_cuota ? 'Primera Cuota' : 'Total a Pagar' }}</span>
                                    <span class="text-purple-600">Bs {{ total.toFixed(2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Cliente -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <i class="fas fa-user text-purple-600"></i> Información del Cliente
                            </h3>
                            <div class="space-y-2">
                                <p><strong>Nombre:</strong> {{ cliente.name }}</p>
                                <p><strong>Email:</strong> {{ cliente.email }}</p>
                                <p><strong>Teléfono:</strong> {{ cliente.telefono || 'No registrado' }}</p>
                            </div>
                        </div>

                        <!-- Instrucciones -->
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                            <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-info-circle text-blue-600"></i> Instrucciones
                            </h3>
                            <ol class="list-decimal list-inside space-y-2 text-blue-800">
                                <li>El código QR se mostrará en el modal a la derecha</li>
                                <li>Abre tu billetera digital (Tigo Money, etc.)</li>
                                <li>Escanea el código QR</li>
                                <li>Confirma el monto de Bs {{ total.toFixed(2) }} {{ es_cuota ? '(Primera Cuota)' : ''
                                    }}</li>
                                <li>Completa el pago</li>
                                <li>{{ es_cuota ? 'La primera cuota se registrará automáticamente' : 'Tu pedido se procesará automáticamente' }}</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Columna Derecha - QR Modal -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
                            <div class="text-center mb-4">
                                <p class="text-sm text-gray-600 font-semibold uppercase tracking-wide">Monto a Pagar</p>
                                <p class="text-4xl font-extrabold text-purple-600 mt-2">Bs {{ total.toFixed(2) }}</p>
                            </div>

                            <div class="bg-purple-50 rounded-lg p-4 mb-4">
                                <p class="text-sm text-purple-700 text-center">
                                    <i class="fas fa-qrcode mr-2"></i> El código QR aparecerá abajo
                                </p>
                            </div>
                        </div>

                        <!-- QR Modal Component -->
                        <QRModal :show="showQRModal" :venta-id="venta_id" :total="total" @close="handleQRClose"
                            @success="handleQRSuccess" />

                        <!-- Botón Volver -->
                        <button @click="volverAlCatalogo"
                            class="w-full bg-gray-200 text-gray-800 py-3 rounded-xl font-medium hover:bg-gray-300 transition-all">
                            <i class="fas fa-arrow-left mr-2"></i> Volver al Catálogo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>

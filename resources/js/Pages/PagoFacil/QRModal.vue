<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    ventaId: {
        type: Number,
        required: true
    },
    total: {
        type: Number,
        required: true
    },
    show: {
        type: Boolean,
        default: false
    },
    cuotaId: { // ID de la cuota si es pago de cuota
        type: Number,
        default: null
    }
});

const emit = defineEmits(['close', 'success', 'error']);

// Estado
const loading = ref(false);
const qrImage = ref(null);
const transactionId = ref(null);
const nroPago = ref(null);
const pagoId = ref(null);
const error = ref(null);
const checkingPayment = ref(false);
const paymentStatus = ref(null);
const pollingInterval = ref(null);
const pollingCount = ref(0);
const maxPollingAttempts = 15; // 50 * 6 segundos = 300 segundos = 5 minutos
const paymentCompleted = ref(false); // Bandera para evitar múltiples notificaciones

// Generar QR
const generarQR = async () => {
    loading.value = true;
    error.value = null;

    try {
        const response = await axios.post(route('pagofacil.generar-qr', { 
            venta_id: props.ventaId, 
            cuota_id: props.cuotaId 
        }), {
            metodo_pago: 'qr' // Datos adicionales se quedan en el body
        });

        if (response.data.success) {
            qrImage.value = response.data.qr_image;
            transactionId.value = response.data.transaction_id;
            nroPago.value = response.data.nro_pago;
            pagoId.value = response.data.pago_id;

            // Iniciar polling para verificar el estado del pago
            iniciarPolling();
        } else {
            error.value = response.data.message || 'Error al generar el QR';
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Error al conectar con el servidor';
        console.error('Error generando QR:', err);
    } finally {
        loading.value = false;
    }
};

// Iniciar polling para verificar estado del pago
const iniciarPolling = () => {
    // Verificar cada 4 segundos (balance entre rapidez y carga de servidor)
    console.log('Iniciando polling de pago cada 10 segundos...');
    pollingInterval.value = setInterval(() => {
        verificarEstadoPago();
    }, 10000);
};

// Detener polling
const detenerPolling = () => {
    if (pollingInterval.value) {
        clearInterval(pollingInterval.value);
        pollingInterval.value = null;
    }
};

// Verificar estado del pago
const verificarEstadoPago = async () => {
    if (!transactionId.value) return;

    pollingCount.value++;
    checkingPayment.value = true;

    console.log(`Polling intento ${pollingCount.value}/${maxPollingAttempts}`);

    try {
        const response = await axios.post(route('pagofacil.consultar-estado'), {
            transaction_id: transactionId.value
        });

        if (response.data.success) {
            const paymentData = response.data.data;
            paymentStatus.value = paymentData.paymentStatus;

            console.log('Verificando estado del pago (v2 - Fixed):', {
                intento: pollingCount.value,
                transactionId: transactionId.value,
                paymentStatus: paymentData.paymentStatus,
                paymentDate: paymentData.paymentDate,
                paymentStatusDescription: paymentData.paymentStatusDescription,
                timestamp: new Date().toLocaleTimeString()
            });

            // IMPORTANTE: PagoFácil usa status 1 para DOS cosas:
            // 1. "En Proceso" (recién generado, SIN paymentDate)
            // 2. "Pagado" (completado, CON paymentDate y paymentTime)
            // Por eso debemos verificar la fecha para status 1
            const status = paymentData.paymentStatus;
            const hasPaymentDate = paymentData.paymentDate && paymentData.paymentTime;

            const isPaid =
                // Status 2 y 5 siempre indican pago completado
                status === 5 || status === '5' ||
                status === 2 || status === '2' ||
                // Status 1 SOLO si tiene fecha y hora de pago
                ((status === 1 || status === '1') && hasPaymentDate);

            if (isPaid && !paymentCompleted.value) {
                paymentCompleted.value = true; // Marcar como completado
                console.log('¡Pago completado detectado!', paymentData);
                detenerPolling();

                // Pequeño delay para asegurar que el backend procesó todo
                setTimeout(() => {
                    emit('success', {
                        pago_id: pagoId.value,
                        transaction_id: transactionId.value,
                        nro_pago: nroPago.value,
                        payment_data: paymentData
                    });
                }, 500);
            } else if (pollingCount.value >= maxPollingAttempts) {
                // Si alcanzamos el máximo de intentos, detener polling
                console.log('⚠️ Máximo de intentos alcanzado. Esperando callback de PagoFácil...');
                detenerPolling();
                // No emitir error, solo esperar el callback
            } else {
                console.log('⏳ Pago aún pendiente...', {
                    intento: pollingCount.value,
                    status: paymentData.paymentStatus,
                    description: paymentData.paymentStatusDescription,
                    hasDate: !!paymentData.paymentDate
                });
            }
        } else {
            // Si retorna success: false pero message es "Datos no encontrados", es normal al principio
            if (response.data.message === 'Datos no encontrados') {
                console.log('⏳ Transacción aún no disponible en PagoFácil...');
            } else {
                console.warn('Respuesta sin éxito:', response.data);
            }
        }
    } catch (err) {
        // Ignorar 404 (Datos no encontrados) ya que es normal durante el polling inicial
        if (err.response && err.response.status === 404) {
            console.log('⏳ Esperando datos de transacción (404)...');
        }
        // Ignorar 503 (PagoFácil timeout) y seguir intentando
        else if (err.response && err.response.status === 503) {
            console.warn('⚠️ API de PagoFácil lenta (timeout), reintentando...');
        }
        else {
            console.error('Error verificando estado del pago:', err);
        }
    } finally {
        checkingPayment.value = false;
    }
};

// Cerrar modal
const cerrar = () => {
    detenerPolling();
    qrImage.value = null;
    transactionId.value = null;
    nroPago.value = null;
    pagoId.value = null;
    error.value = null;
    paymentStatus.value = null;
    paymentCompleted.value = false; // Resetear bandera
    emit('close');
};

// Descargar QR
const descargarQR = () => {
    if (!qrImage.value) return;

    const link = document.createElement('a');
    link.href = qrImage.value;
    link.download = `qr-pago-${nroPago.value}.png`;
    link.click();
};

// Copiar número de pago
const copiarNroPago = () => {
    if (nroPago.value) {
        navigator.clipboard.writeText(nroPago.value);
        alert('Número de pago copiado al portapapeles');
    }
};

// Lifecycle
onMounted(() => {
    if (props.show) {
        generarQR();
    }
});

// Watch para cuando el modal se abre después del montaje
watch(() => props.show, (newValue, oldValue) => {
    if (newValue && !oldValue && !qrImage.value) {
        generarQR();
    }
});

onUnmounted(() => {
    detenerPolling();
});
</script>

<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div
                class="sticky top-0 bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fas fa-qrcode text-2xl"></i>
                    <h2 class="text-xl font-bold">Código QR de Pago</h2>
                </div>
                <button @click="cerrar" class="text-white hover:bg-purple-800 p-2 rounded-lg transition">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Contenido -->
            <div class="p-6 space-y-6">
                <!-- Monto -->
                <div class="bg-purple-50 rounded-xl p-4 border border-purple-200">
                    <p class="text-sm text-purple-600 font-medium">Monto a Pagar</p>
                    <p class="text-3xl font-bold text-purple-700">Bs. {{ total.toFixed(2) }}</p>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="flex flex-col items-center justify-center py-8">
                    <div class="animate-spin mb-4">
                        <i class="fas fa-spinner text-4xl text-purple-600"></i>
                    </div>
                    <p class="text-gray-600">Generando código QR...</p>
                </div>

                <!-- Error -->
                <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-red-600 text-xl mt-1"></i>
                        <div>
                            <p class="font-semibold text-red-800">Error</p>
                            <p class="text-sm text-red-700">{{ error }}</p>
                        </div>
                    </div>
                    <button @click="generarQR"
                        class="mt-4 w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition font-medium">
                        Reintentar
                    </button>
                </div>

                <!-- QR Success -->
                <div v-else-if="qrImage" class="space-y-4">
                    <!-- QR Image -->
                    <div class="bg-gray-50 rounded-xl p-4 flex justify-center border border-gray-200">
                        <img :src="qrImage" alt="Código QR" class="w-64 h-64 object-contain">
                    </div>

                    <!-- Número de Pago -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <p class="text-xs text-gray-600 font-medium mb-2">Número de Referencia</p>
                        <div class="flex items-center gap-2">
                            <code
                                class="flex-1 bg-white p-2 rounded border border-gray-300 text-sm font-mono text-gray-800 truncate">
                                {{ nroPago }}
                            </code>
                            <button @click="copiarNroPago"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-700 p-2 rounded transition"
                                title="Copiar">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Instrucciones -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <p class="text-sm font-semibold text-blue-900 mb-2">
                            <i class="fas fa-info-circle mr-2"></i> Instrucciones
                        </p>
                        <ul class="text-xs text-blue-800 space-y-1">
                            <li>✓ Escanea este código QR con tu billetera digital</li>
                            <li>✓ Confirma el monto a pagar</li>
                            <li>✓ Completa la transacción</li>
                            <li>✓ Tu pedido se procesará automáticamente</li>
                        </ul>
                    </div>

                    <!-- Estado de Verificación -->
                    <div v-if="checkingPayment"
                        class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center gap-3">
                        <div class="animate-spin">
                            <i class="fas fa-spinner text-yellow-600"></i>
                        </div>
                        <p class="text-sm text-yellow-800">Verificando estado del pago...</p>
                    </div>

                    <!-- Botones -->
                    <div class="space-y-2">
                        <button @click="descargarQR"
                            class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-download"></i> Descargar QR
                        </button>
                        <button @click="cerrar"
                            class="w-full bg-gray-200 text-gray-800 py-3 rounded-lg hover:bg-gray-300 transition font-medium">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}
</style>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ResumenProductos from './Components/ResumenProductos.vue';
import InfoCliente from './Components/InfoCliente.vue';
import MetodoPago from './Components/MetodoPago.vue';
import BotonConfirmar from './Components/BotonConfirmar.vue';
import DebugPanel from './Components/DebugPanel.vue';
import QRModal from '@/Pages/PagoFacil/QRModal.vue';
import StripePaymentModal from '@/Components/StripePaymentModal.vue';

const props = defineProps({
    productos: { type: Array, default: () => [] },
    cliente: { type: Object, default: () => ({}) },
    total: { type: Number, default: 0 },
    venta_id: { type: Number, default: null }
});

// Estado local
const tipoPago = ref('efectivo');
const modalidadPago = ref('contado');
const numeroCuotas = ref(3);
const processing = ref(false);

// Datos locales
const localProductos = ref([]);
const localTotal = ref(0);
const localCliente = ref({});

// Estado del QR
const showQRModal = ref(false);
const ventaIdParaQR = ref(null);

// Estado Stripe
const showStripeModal = ref(false);
const amountForStripe = ref(0);

// Calcular si hay datos válidos
const hayDatos = computed(() => localProductos.value.length > 0);

// Inicializar datos
onMounted(() => {
    // Usar props si están disponibles
    if (props.productos && props.productos.length > 0) {
        localProductos.value = props.productos;
        localTotal.value = props.total || calcularTotal(props.productos);
        localCliente.value = props.cliente || {};
    }
    // Fallback: intentar recuperar de localStorage
    else if (typeof localStorage !== 'undefined') {
        const backup = localStorage.getItem('carrito_backup');
        if (backup) {
            try {
                localProductos.value = JSON.parse(backup);
                localTotal.value = calcularTotal(localProductos.value);
            } catch (e) {
                console.error('Error parseando localStorage:', e);
            }
        }
    }
});

const calcularTotal = (items) => {
    return items.reduce((sum, item) => sum + (Number(item.precio || 0) * Number(item.cantidad || 1)), 0);
};

// Estado local adicional
const cuotasSchedule = ref([]);

const handlePlanConfirmed = (items) => {
    cuotasSchedule.value = items;
};

const finalizarCompra = () => {
    if (modalidadPago.value === 'cuotas' && cuotasSchedule.value.length === 0) {
        alert('Por favor configura y confirma el plan de pagos antes de continuar.');
        return;
    }

    // Calcular total inicial (si es cuotas, solo la 1ra, sino todo)
    let montoTotalTransaccion = localTotal.value;
    if (modalidadPago.value === 'cuotas') {
        const primerPago = cuotasSchedule.value.find(c => c.numero === 1);
        if (primerPago) {
            montoTotalTransaccion = primerPago.monto;
        }
    }

    // VALIDACIÓN: Pagos con tarjeta deben ser mayores a 5 Bs
    if (tipoPago.value === 'tarjeta' && montoTotalTransaccion <= 5) {
        alert('⚠️ Restricción de Pago con Tarjeta\n\nLos pagos con tarjeta deben ser mayores a 5 Bs.\n\nMonto actual: Bs ' + montoTotalTransaccion.toFixed(2) + '\n\nPor favor, selecciona otro método de pago (Efectivo o QR).');
        return;
    }

    if (!confirm('¿Confirmar compra?')) return;

    processing.value = true;

    // INTERCEPTAR PAGO CON TARJETA
    if (tipoPago.value === 'tarjeta') {
        amountForStripe.value = montoTotalTransaccion;
        showStripeModal.value = true;
        processing.value = false; // Detener spinner global
        return;
    }

    submitOrder(montoTotalTransaccion);
};

const submitOrder = (montoInicial, stripePaymentIntent = null) => {
    router.post(route('catalogo.confirmar'), {
        productos: localProductos.value,
        cliente_id: localCliente.value?.id,
        tipo_pago: tipoPago.value,
        modalidad_pago: modalidadPago.value,
        numero_cuotas: modalidadPago.value === 'cuotas' ? numeroCuotas.value : null,
        cuotas_schedule: modalidadPago.value === 'cuotas' ? cuotasSchedule.value : null,
        pago_inicial: montoInicial,
        total: localTotal.value,
        stripe_payment_id: stripePaymentIntent?.id
    }, {
        onFinish: () => {
            processing.value = false;
        },
        onError: (errors) => {
            processing.value = false;
            alert('Error al procesar el pedido. Por favor intente nuevamente.');
            console.error('Errores:', errors);
        }
    });
};

const handleStripeSuccess = (paymentIntent) => {
    showStripeModal.value = false;
    alert('¡Pago con tarjeta exitoso!');
    processing.value = true;
    submitOrder(amountForStripe.value, paymentIntent);
};

const handleQRSuccess = (data) => {
    showQRModal.value = false;
    if (typeof localStorage !== 'undefined') {
        localStorage.removeItem('carrito_backup');
    }
    alert('¡Pago completado! Tu pedido ha sido procesado correctamente.');
    router.visit(route('cliente.pedidos.index'));
};

const handleQRClose = () => {
    showQRModal.value = false;
    // El usuario puede cerrar el modal sin completar el pago
    // El pago se puede completar después escaneando el QR
};

const volverAlCatalogo = () => {
    router.visit(route('catalogo.index'));
};
</script>

<template>
    <AppLayout title="Finalizar Compra">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-amber-600"></i> Finalizar Compra
                </h2>
                <div class="text-sm text-gray-600">
                    Paso 2 de 2: Confirmar y Pagar
                </div>
            </div>
        </template>

        <!-- Estado Vacío / Error -->
        <div v-if="!hayDatos" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-10 text-center">
                    <div class="inline-block p-4 bg-amber-50 rounded-full mb-4">
                        <i class="fas fa-shopping-basket text-4xl text-amber-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Tu carrito está vacío</h3>
                    <p class="mt-2 text-gray-500">Parece que no hay productos para procesar.</p>
                    <div class="mt-6">
                        <button @click="volverAlCatalogo"
                            class="bg-amber-600 text-white px-6 py-2 rounded-lg hover:bg-amber-700 transition">
                            Volver al Catálogo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div v-else class="py-8 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Columna Izquierda -->
                    <div class="lg:col-span-2 space-y-6">
                        <ResumenProductos :productos="localProductos" :total="localTotal" />
                        <InfoCliente :cliente="localCliente" />
                    </div>

                    <!-- Columna Derecha -->
                    <div class="space-y-6">
                        <MetodoPago v-model:tipoPago="tipoPago" v-model:modalidadPago="modalidadPago"
                            v-model:numeroCuotas="numeroCuotas" :total="localTotal"
                            @plan-confirmed="handlePlanConfirmed" />
                        <BotonConfirmar :total="localTotal" :modalidadPago="modalidadPago" :numeroCuotas="numeroCuotas"
                            :processing="processing" @confirmar="finalizarCompra" />
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Modal -->
        <QRModal :show="showQRModal" :venta-id="ventaIdParaQR" :total="localTotal" @close="handleQRClose"
            @success="handleQRSuccess" />

        <!-- Stripe Modal -->
        <StripePaymentModal :show="showStripeModal" :amount="amountForStripe" description="Pago de Pedido"
            @close="showStripeModal = false" @success="handleStripeSuccess" />
    </AppLayout>
</template>

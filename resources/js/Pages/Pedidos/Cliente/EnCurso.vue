<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ActividadPedido from './Components/ActividadPedido.vue';
import DialogModal from '@/Components/DialogModal.vue';
import StripePaymentModal from '@/Components/StripePaymentModal.vue';
import QRModal from '@/Pages/PagoFacil/QRModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
  pedido: Object,
  detalles: Array
});

const marcarRecibido = () => router.put(route('cliente.pedidos.recibido', props.pedido.id));

// Lógica de Cuotas
const cuotas = computed(() => props.pedido?.venta?.cuotas || []);
const hayCuotas = computed(() => cuotas.value.length > 0);

const isAtrasado = (fecha) => {
  return new Date(fecha) < new Date() && !fecha.match(/pagado/i);
  // Nota: la lógica real dependería del estado 'atrasado' en BD o fecha vs hoy si estado != pagado
};

// Formato de fecha: DD-MM-AAAA
const formatDate = (isoString) => {
  if (!isoString) return '';
  const datePart = isoString.split('T')[0];
  const [year, month, day] = datePart.split('-');
  return `${day}-${month}-${year}`;
};

const showPaymentModal = ref(false);
const cuotaSeleccionada = ref(null);

// Estado Stripe
const showStripeModal = ref(false);

// Estado QR
const showQRModal = ref(false);

const abrirPago = (cuota) => {
  cuotaSeleccionada.value = cuota;
  showPaymentModal.value = true;
};

const pagarMock = (metodo) => {
  if (metodo === 'Tarjeta') {
    showPaymentModal.value = false;
    showStripeModal.value = true;
    return;
  }

  if (metodo === 'QR') {
    console.log('Abriendo QR Modal:', {
      venta_id: props.pedido.venta?.id,
      cuota_id: cuotaSeleccionada.value?.id,
      monto: cuotaSeleccionada.value?.monto,
      tiene_venta: !!props.pedido.venta
    });
    showPaymentModal.value = false;
    showQRModal.value = true;
  }
};

const handleQRSuccess = (data) => {
  showQRModal.value = false;
  // Recargar la página para mostrar la cuota actualizada
  router.reload({ only: ['pedido'] });
};

const handleQRClose = () => {
  showQRModal.value = false;
};

const handleStripeSuccess = (paymentIntent) => {
  showStripeModal.value = false;
  // Call backend to mark as paid
  router.put(route('pedidos.cuota.pagar', cuotaSeleccionada.value.id), {
    payment_id: paymentIntent.id
  }, {
    onSuccess: () => alert('¡Cuota pagada exitosamente!'),
    onError: () => alert('Error al registrar el pago en el sistema.')
  });
};
</script>

<template>
  <AppLayout title="Pedido en curso">
    <template #header>
      <h2 class="font-semibold text-xl">Pedido #{{ pedido.id }}</h2>
    </template>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow p-4 lg:col-span-2">
          <h3 class="font-bold mb-3">Detalle</h3>
          <ul class="space-y-2">
            <li v-for="d in detalles" :key="d.id" class="flex justify-between">
              <span>{{ d.producto?.nombre || 'Producto' }} x {{ d.cantidad }}</span>
              <span class="font-semibold">Bs {{ Number(d.subtotal).toFixed(2) }}</span>
            </li>
          </ul>
          <div class="mt-4 border-t pt-4">
            <p class="text-sm text-gray-600">Estado: {{ pedido.estado_produccion }}</p>
            <p class="text-sm text-gray-600 font-bold mt-2">Total: Bs {{ Number(pedido.total).toFixed(2) }}</p>
          </div>

          <!-- Plan de Pagos -->
          <div v-if="hayCuotas" class="mt-8 border-t pt-6">
            <h3 class="font-bold text-lg text-blue-800 mb-4">Plan de Pagos (Cuotas)</h3>
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">#</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Vencimiento</th>
                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Monto</th>
                    <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Estado</th>
                    <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Acción</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="cuota in cuotas" :key="cuota.id">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">{{ cuota.numero_cuota
                      }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                      {{ formatDate(cuota.fecha_vencimiento) }}
                      <span v-if="cuota.estado === 'pendiente' && isAtrasado(cuota.fecha_vencimiento)"
                        class="text-red-600 font-bold ml-1">(Atrasado)</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-gray-900 font-mono">
                      Bs {{ Number(cuota.monto).toFixed(2) }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                      <span v-if="cuota.estado === 'pagado'"
                        class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Pagado</span>
                      <span v-else-if="cuota.estado === 'atrasado'"
                        class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Atrasado</span>
                      <span v-else
                        class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Pendiente</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                      <button v-if="cuota.estado !== 'pagado'" @click="abrirPago(cuota)"
                        class="text-indigo-600 hover:text-indigo-900 font-medium">
                        Pagar
                      </button>
                      <span v-else class="text-gray-400">-</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow p-4">
          <ActividadPedido :estado="pedido.estado_produccion" />
          <button class="mt-4 w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition"
            @click="marcarRecibido">
            Marcar como recibido
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Pago -->
    <DialogModal :show="showPaymentModal" @close="showPaymentModal = false">
      <template #title>
        Pagar Cuota #{{ cuotaSeleccionada?.numero_cuota }}
      </template>
      <template #content>
        <div class="text-center">
          <p class="mb-4 text-lg">Monto a pagar: <strong>Bs {{ cuotaSeleccionada?.monto }}</strong></p>

          <div class="grid grid-cols-2 gap-4">
            <button @click="pagarMock('QR')"
              class="p-4 border rounded-lg hover:bg-purple-50 hover:border-purple-500 transition flex flex-col items-center">
              <i class="fas fa-qrcode text-3xl text-purple-600 mb-2"></i>
              <span class="font-bold text-gray-700">QR Simple</span>
            </button>
            <button @click="pagarMock('Tarjeta')"
              class="p-4 border rounded-lg hover:bg-blue-50 hover:border-blue-500 transition flex flex-col items-center">
              <i class="fas fa-credit-card text-3xl text-blue-600 mb-2"></i>
              <span class="font-bold text-gray-700">Tarjeta</span>
            </button>
          </div>
        </div>
      </template>
      <template #footer>
        <SecondaryButton @click="showPaymentModal = false">
          Cancelar
        </SecondaryButton>
      </template>
    </DialogModal>

    <!-- Stripe Modal -->
    <StripePaymentModal :show="showStripeModal" :amount="Number(cuotaSeleccionada?.monto || 0)"
      :description="`Pago Cuota #${cuotaSeleccionada?.numero_cuota}`" @close="showStripeModal = false"
      @success="handleStripeSuccess" />

    <!-- QR Modal -->
    <QRModal v-if="cuotaSeleccionada && pedido.venta" :show="showQRModal" :venta-id="pedido.venta?.id || 0"
      :total="Number(cuotaSeleccionada?.monto || 0)" :cuota-id="cuotaSeleccionada?.id" @close="handleQRClose"
      @success="handleQRSuccess" />

  </AppLayout>
</template>

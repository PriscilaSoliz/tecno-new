<script setup>
import { onMounted, ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
  pedido: Object,
  detalles: Array,
  deliveryRoute: Object
});

let map, storeMarker, customerMarker, routeLayer;

async function ensureLeaflet() {
  if (typeof window === 'undefined') return;
  if (window.L) { L = window.L; return; }

  // inject CSS once
  if (!document.getElementById('leaflet-css')) {
    const link = document.createElement('link');
    link.id = 'leaflet-css';
    link.rel = 'stylesheet';
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(link);
  }

  // inject script and await load
  if (!document.getElementById('leaflet-script')) {
    await new Promise((resolve, reject) => {
      const s = document.createElement('script');
      s.id = 'leaflet-script';
      s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
      s.async = true;
      s.onload = () => resolve(true);
      s.onerror = (e) => reject(e);
      document.body.appendChild(s);
    });
  } else {
    // if script already present, wait until global is available
    await new Promise((resolve) => {
      const timer = setInterval(() => {
        if (window.L) {
          clearInterval(timer);
          resolve(true);
        }
      }, 50);
    });
  }

  L = window.L;
}

async function initMap() {
  await ensureLeaflet();

  const origin = props.deliveryRoute.origin;
  const dest = props.deliveryRoute.destination;
  const centerLat = (origin.lat + dest.lat) / 2;
  const centerLng = (origin.lng + dest.lng) / 2;

  map = L.map('delivery-map').setView([centerLat, centerLng], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>'
  }).addTo(map);

  storeMarker = L.marker([origin.lat, origin.lng], { title: 'Tienda' }).addTo(map);
  customerMarker = L.marker([dest.lat, dest.lng], { title: 'Cliente' }).addTo(map);

  const latlngs = [[origin.lat, origin.lng], [dest.lat, dest.lng]];
  routeLayer = L.polyline(latlngs, { color: 'blue' }).addTo(map);
  map.fitBounds(routeLayer.getBounds(), { padding: [20, 20] });
}

// Acciones de estado
const updateEstado = (nuevoEstado) => {
  router.put(route('pedidos.update', props.pedido.id), { estado_produccion: nuevoEstado });
};

const estadoActual = computed(() => props.pedido.estado_produccion);

// Lógica de Cuotas
const cuotas = computed(() => props.pedido?.venta?.cuotas || []);
const hayCuotas = computed(() => cuotas.value.length > 0);

const formatDate = (isoString) => {
  if (!isoString) return '';
  const datePart = isoString.split('T')[0];
  const [year, month, day] = datePart.split('-');
  return `${day}-${month}-${year}`;
};

const isAtrasado = (fecha) => {
  return new Date(fecha) < new Date() && !fecha.match(/pagado/i);
};

onMounted(async () => await initMap());
</script>

<template>
  <AppLayout title="Seguimiento de Pedido">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl">Pedido #{{ pedido.id }}</h2>
        <Link :href="route('pedidos.index')" 
              class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
          <i class="fas fa-arrow-left mr-2"></i> Atrás
        </Link>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Mapa -->
          <div class="bg-white rounded-xl shadow p-4 lg:col-span-2">
            <div id="delivery-map" class="w-full h-[400px] rounded"></div>
          </div>
          <!-- Detalles -->
          <div class="bg-white rounded-xl shadow p-4">
            <h3 class="font-bold mb-3">Detalle del Pedido</h3>
            <ul class="space-y-2">
              <li v-for="d in detalles" :key="d.id" class="flex justify-between">
                <span>{{ d.producto?.nombre || 'Producto' }} x {{ d.cantidad }}</span>
                <span class="font-semibold">Bs {{ Number(d.subtotal).toFixed(2) }}</span>
              </li>
            </ul>
            <div class="mt-4 border-t pt-4">
              <h4 class="font-bold text-gray-800 mb-2">Información del Cliente</h4>
              <div class="bg-gray-50 p-3 rounded-lg mb-4 text-sm">
                <p class="text-gray-900 font-medium">
                  <i class="fas fa-user mr-2 text-gray-400"></i> 
                  {{ pedido.cliente?.user?.name || 'Cliente sin nombre' }}
                </p>
                <p v-if="pedido.cliente?.user?.telefono" class="text-gray-600 mt-1">
                  <i class="fas fa-phone mr-2 text-gray-400"></i> 
                  {{ pedido.cliente?.user?.telefono }}
                </p>
                <p v-if="pedido.cliente?.user?.email" class="text-gray-600 mt-1">
                  <i class="fas fa-envelope mr-2 text-gray-400"></i> 
                  {{ pedido.cliente?.user?.email }}
                </p>
              </div>

              <div class="flex justify-between items-center border-t pt-2">
                <span class="text-gray-600">Estado: <span class="font-bold uppercase ml-1"
                    :class="{ 'text-green-600': estadoActual === 'completed' }">{{ estadoActual }}</span></span>
                <span class="text-lg font-bold text-gray-900">Total: Bs {{ Number(pedido.total).toFixed(2) }}</span>
              </div>
            </div>

            <!-- Plan de Pagos (Admin View) -->
            <div v-if="hayCuotas" class="mt-6 border-t pt-4">
              <h4 class="font-bold text-blue-800 mb-3">Plan de Pagos</h4>
              <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="py-2 pl-3 pr-2 text-left text-xs font-semibold text-gray-900">#</th>
                      <th scope="col" class="px-2 py-2 text-left text-xs font-semibold text-gray-900">Vence</th>
                      <th scope="col" class="px-2 py-2 text-right text-xs font-semibold text-gray-900">Monto</th>
                      <th scope="col" class="px-2 py-2 text-center text-xs font-semibold text-gray-900">Estado</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="cuota in cuotas" :key="cuota.id">
                      <td class="whitespace-nowrap py-2 pl-3 pr-2 text-xs font-medium text-gray-900">{{
                        cuota.numero_cuota
                        }}</td>
                      <td class="whitespace-nowrap px-2 py-2 text-xs text-gray-500">
                        {{ formatDate(cuota.fecha_vencimiento) }}
                        <span v-if="cuota.estado === 'pendiente' && isAtrasado(cuota.fecha_vencimiento)"
                          class="text-red-600 font-bold ml-1">!</span>
                      </td>
                      <td class="whitespace-nowrap px-2 py-2 text-xs text-right text-gray-900 font-mono">
                        Bs {{ Number(cuota.monto).toFixed(2) }}
                      </td>
                      <td class="whitespace-nowrap px-2 py-2 text-xs text-center">
                        <span v-if="cuota.estado === 'pagado'"
                          class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Pagado</span>
                        <span v-else-if="cuota.estado === 'atrasado'"
                          class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Atrasado</span>
                        <span v-else
                          class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">Pendiente</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Flujo de Botones -->
            <div class="mt-6">
              <button v-if="estadoActual === 'pending'"
                class="w-full px-4 py-3 bg-amber-600 text-white rounded-lg font-bold hover:bg-amber-700 transition"
                @click="updateEstado('assigned')">
                <i class="fas fa-user-check mr-2"></i> Asignar Delivery
              </button>

              <button v-else-if="estadoActual === 'assigned'"
                class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition"
                @click="updateEstado('on_route')">
                <i class="fas fa-motorcycle mr-2"></i> Marcar En Camino
              </button>

              <div v-else-if="estadoActual === 'on_route'"
                class="text-center p-3 bg-blue-50 text-blue-800 rounded-lg border border-blue-200">
                <i class="fas fa-clock mr-2"></i> Esperando confirmación del cliente...
              </div>

              <button v-else-if="estadoActual === 'delivered'"
                class="w-full px-4 py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition"
                @click="updateEstado('completed')">
                <i class="fas fa-check-double mr-2"></i> Confirmar Entrega
              </button>

              <div v-else-if="estadoActual === 'completed'"
                class="text-center p-3 bg-green-50 text-green-800 rounded-lg border border-green-200 font-bold">
                <i class="fas fa-flag-checkered mr-2"></i> Pedido Completado
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

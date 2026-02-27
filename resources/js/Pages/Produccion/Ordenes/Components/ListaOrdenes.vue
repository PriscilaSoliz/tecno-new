<template>
  <div>
    <div class="p-6 border-b border-gray-200">
      <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="flex-1">
          <div class="relative">
            <input v-model="searchQuery" placeholder="Buscar por producto o ID..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
          </div>
        </div>

        <div class="flex gap-2 items-center">
          <select v-model="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 text-sm">
            <option value="">Todos</option>
            <option value="PENDIENTE">Pendientes</option>
            <option value="EN_PROCESO">En Proceso</option>
            <option value="FINALIZADA">Finalizadas</option>
            <option value="CANCELADA">Canceladas</option>
          </select>

          <select v-model="operarioFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 text-sm">
            <option value="">Todos los operarios</option>
            <option v-for="op in operarios" :key="op.id" :value="op.id">{{ op.turno }} - {{ op.especialidad }}</option>
          </select>
        </div>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Producto</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Cantidad</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Operario</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Fecha</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Estado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="o in sorted" :key="o.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ o.id }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ o.producto?.nombre || 'N/A' }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ o.cantidad_a_producir }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ o.operario ? (o.operario.turno + ' / ' + o.operario.especialidad) : '-' }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ new Date(o.fecha_creacion).toLocaleString() }}</td>
            <td class="px-6 py-4 text-sm">
              <span :class="statusClass(o.estado)">{{ o.estado }}</span>
            </td>
            <td class="px-6 py-4 text-sm text-right">
              <div class="flex items-center justify-end space-x-2">
                <!-- Principal action visible -->
                <button v-if="o.estado === 'pendiente'"
                        @click="$emit('ejecutar', o)"
                        class="px-3 py-1 bg-amber-600 text-white rounded hover:bg-amber-700">
                  <i class="fas fa-play mr-1"></i> Iniciar
                </button>

                <button v-else-if="o.estado === 'en_proceso'"
                        @click="$emit('finalizar', o)"
                        class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                  <i class="fas fa-check mr-1"></i> Finalizar
                </button>

                <!-- small submenu trigger -->
                <div class="relative">
                  <button @click="toggleMenu(o.id)" class="p-2 bg-white border rounded-full hover:shadow-sm">
                    <i class="fas fa-ellipsis-v text-gray-600"></i>
                  </button>

                  <!-- submenu -->
                  <div v-if="openMenuId === o.id" class="absolute right-0 mt-2 w-44 bg-white border rounded shadow-lg z-50 text-left">
                    <button @click="$emit('edit', o)" class="w-full text-left px-3 py-2 hover:bg-gray-50">Editar</button>
                    <button @click="$emit('consume', o, 'peps')" class="w-full text-left px-3 py-2 hover:bg-gray-50">Consumir (PEPS)</button>
                    <button @click="$emit('consume', o, 'ueps')" class="w-full text-left px-3 py-2 hover:bg-gray-50">Consumir (UEPS)</button>
                    <div class="border-t my-1"></div>
                    <button @click="$emit('delete', o)" class="w-full text-left px-3 py-2 text-rose-600 hover:bg-gray-50">Eliminar</button>
                  </div>
                </div>
              </div>
            </td>
          </tr>

          <tr v-if="filtered.length === 0">
            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No se encontraron órdenes</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
const props = defineProps({
  orders: { type: Array, default: () => [] },
  products: { type: Array, default: () => [] },
  operarios: { type: Array, default: () => [] },
});

const searchQuery = ref('');
const statusFilter = ref('');
const operarioFilter = ref('');
const sortField = ref('id');
const sortDirection = ref('desc');
const openMenuId = ref(null);

const filtered = computed(() => {
  const q = (searchQuery.value || '').toString().toLowerCase();
  return props.orders.filter(o => {
    const prod = (o.producto?.nombre || '').toString().toLowerCase();
    const matchesSearch = !q || prod.includes(q) || String(o.id) === q;
    if (statusFilter.value && o.estado !== statusFilter.value) return false;
    if (operarioFilter.value && String(o.operario_id) !== String(operarioFilter.value)) return false;
    return matchesSearch;
  });
});

const sorted = computed(() => {
  return filtered.value.slice().sort((a,b) => {
    let A = a[sortField.value], B = b[sortField.value];
    if (typeof A === 'string') { A=A.toLowerCase(); B=(B||'').toLowerCase(); }
    if (A < B) return sortDirection.value === 'asc' ? -1 : 1;
    if (A > B) return sortDirection.value === 'asc' ? 1 : -1;
    return 0;
  });
});

const statusClass = (estado) => {
  // manejar los valores en lowercase que usa la migración
  if (estado === 'pendiente') return 'px-2 py-1 rounded text-sm bg-yellow-50 text-yellow-700';
  if (estado === 'en_proceso') return 'px-2 py-1 rounded text-sm bg-orange-50 text-orange-700';
  if (estado === 'finalizada') return 'px-2 py-1 rounded text-sm bg-green-50 text-green-700';
  if (estado === 'cancelada') return 'px-2 py-1 rounded text-sm bg-red-50 text-red-700';
  return 'px-2 py-1 rounded text-sm bg-gray-50 text-gray-700';
};

const toggleMenu = (id) => {
	openMenuId.value = openMenuId.value === id ? null : id;
};
</script>

<style scoped>
/* minimal */

/* menu small-box style */
</style>

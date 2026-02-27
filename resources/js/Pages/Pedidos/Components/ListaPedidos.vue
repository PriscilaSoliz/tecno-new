<script setup>
const props = defineProps({ items: { type: Array, default: () => [] } });
const emit = defineEmits(['ver', 'quick']);

const estadoColor = {
  pending: 'bg-yellow-100 text-yellow-800',
  assigned: 'bg-blue-100 text-blue-800',
  on_route: 'bg-purple-100 text-purple-800',
  delivered: 'bg-green-100 text-green-800',
  completed: 'bg-gray-100 text-gray-800',
};

const estadoLabel = {
  pending: 'Pendiente',
  assigned: 'Asignado',
  on_route: 'En camino',
  delivered: 'Entregado',
  completed: 'Completado',
};

// Obtener roles del usuario desde Inertia
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const userRoles = (Array.isArray(page.props.auth?.user?.roles) ? page.props.auth.user.roles : [])
  .map(r => (typeof r === 'object' && r !== null ? (r.name ?? r) : String(r)));

// habilitar avance solo a roles de staff
const canAdvance = userRoles.some(r => ['propietario', 'encargadoalmacen', 'produccion', 'delivery', 'operario'].includes(r));
</script>

<template>
  <table class="w-full">
    <thead class="bg-gray-50 dark:bg-gray-700">
      <tr>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">ID</th>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">Cliente</th>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">Fecha</th>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">Total</th>
        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">Estado</th>
        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">Acciones</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
      <tr v-for="p in items" :key="p.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">#{{ p.id }}</td>
        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ p.cliente?.nombre || p.cliente?.name || 'Cliente' }}
        </td>
        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ new Date(p.created_at).toLocaleString() }}</td>
        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">Bs {{ Number(p.total).toFixed(2) }}</td>
        <td class="px-4 py-3">
          <div class="flex flex-col gap-1 items-start">
            <span
              :class="[estadoColor[p.estado_produccion] || 'bg-gray-100 text-gray-800', 'px-2 py-1 rounded-full text-xs font-medium']">
              {{ estadoLabel[p.estado_produccion] || p.estado_produccion }}
            </span>
            <!-- Indicador de Pago Pendiente -->
            <span v-if="p.venta?.cuotas?.some(c => c.estado !== 'pagado')"
              class="px-2 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200">
              <i class="fas fa-clock mr-1"></i> Pago Pendiente
            </span>
          </div>
        </td>
        <td class="px-4 py-3 text-right space-x-2">
          <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm" @click="emit('ver', p)">
            <i class="fa-solid fa-eye mr-1"></i> Ver
          </button>

          <!-- Mostrar Avanzar solo para usuarios con permiso (no clientes) -->
          <button v-if="canAdvance && p.estado_produccion !== 'completed'"
            class="px-3 py-1 bg-amber-600 text-white rounded hover:bg-amber-700 text-sm" @click="emit('quick', p)">
            <i class="fa-solid fa-forward mr-1"></i> Avanzar
          </button>
        </td>
      </tr>
      <tr v-if="items.length === 0">
        <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
          <i class="fa-solid fa-inbox text-4xl mb-2 text-gray-300"></i>
          <p>Sin pedidos</p>
        </td>
      </tr>
    </tbody>
  </table>
</template>

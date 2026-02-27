<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListaPedidos from './Components/ListaPedidos.vue';

const props = defineProps({
  pedidos: { type: Array, default: () => [] }
});

const search = ref('');
const estadoFilter = ref('');

const estadoColor = {
  pending: 'bg-yellow-100 text-yellow-800',
  assigned: 'bg-blue-100 text-blue-800',
  on_route: 'bg-purple-100 text-purple-800',
  delivered: 'bg-green-100 text-green-800',
  completed: 'bg-gray-100 text-gray-800',
};

const filtered = computed(() => {
  let result = props.pedidos;
  
  const q = (search.value || '').toLowerCase();
  if (q) {
    result = result.filter(p => {
      const cliente = (p.cliente?.nombre || p.cliente?.name || '').toLowerCase();
      return cliente.includes(q) || String(p.id) === q;
    });
  }

  if (estadoFilter.value) {
    result = result.filter(p => p.estado_produccion === estadoFilter.value);
  }

  return result;
});

const verPedido = (pedido) => router.get(route('pedidos.show', pedido.id));
const quickComplete = (pedido) => router.post(route('pedidos.quick-complete', pedido.id));
</script>

<template>
  <AppLayout title="Pedidos">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Gestión de Pedidos</h2>
        <div class="flex gap-2">
          <select v-model="estadoFilter" class="border rounded px-3 py-2">
            <option value="">Todos los estados</option>
            <option value="pending">Pendiente</option>
            <option value="assigned">Asignado</option>
            <option value="on_route">En camino</option>
            <option value="delivered">Entregado</option>
            <option value="completed">Completado</option>
          </select>
          <input v-model="search" placeholder="Buscar por cliente o ID" class="border rounded px-3 py-2" />
        </div>
      </div>
    </template>

    <div class="py-8">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
          <ListaPedidos 
            :items="filtered"
            @ver="verPedido"
            @quick="quickComplete" 
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

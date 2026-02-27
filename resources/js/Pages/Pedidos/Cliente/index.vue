<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListaPedidos from '../Components/ListaPedidos.vue';

const props = defineProps({
  enCurso: { type: Array, default: () => [] },
  realizados: { type: Array, default: () => [] }
});

const search = ref('');
const norm = t => (t||'').toLowerCase();
const filtrar = list => list.filter(p => norm(p.cliente?.nombre || p.id.toString()).includes(norm(search.value)));

const ver = p => router.get(route('cliente.pedidos.show', p.id));
</script>

<template>
  <AppLayout title="Mis Pedidos">
    <template #header>
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
          <button @click="router.visit(route('catalogo.index'))" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Ir al Catálogo
          </button>
          <h2 class="font-semibold text-xl">Mis Pedidos</h2>
        </div>
        <input v-model="search" placeholder="Buscar pedido..." class="border rounded px-3 py-2 text-sm" />
      </div>
    </template>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white shadow sm:rounded-lg p-6 mb-6">
        <h3 class="font-bold mb-3 text-lg text-amber-700 border-b pb-2">En curso</h3>
        <ListaPedidos :items="filtrar(props.enCurso)" @ver="ver" />
      </div>
      <div class="bg-white shadow sm:rounded-lg p-6">
        <h3 class="font-bold mb-3 text-lg text-gray-700 border-b pb-2">Historial</h3>
        <ListaPedidos :items="filtrar(props.realizados)" @ver="ver" />
      </div>
    </div>
  </AppLayout>
</template>

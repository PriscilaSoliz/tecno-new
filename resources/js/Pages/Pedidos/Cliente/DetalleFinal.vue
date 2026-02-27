<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ pedido: Object });
</script>

<template>
  <AppLayout title="Pedido entregado">
    <template #header>
      <h2 class="font-semibold text-xl">Pedido #{{ pedido.id }} entregado</h2>
    </template>

    <div class="py-8 max-w-3xl mx-auto">
      <div class="bg-white rounded-xl shadow p-6 text-center">
        <p class="text-lg mb-2">¡Gracias por tu compra!</p>
        <p class="text-sm text-gray-600 mb-6">Estado: {{ pedido.estado_produccion }}</p>
        <button class="px-4 py-2 bg-amber-600 text-white rounded" @click="router.get(route('cliente.pedidos.index'))">
          Volver a Mis Pedidos
        </button>
      </div>
    </div>
  </AppLayout>
</template>

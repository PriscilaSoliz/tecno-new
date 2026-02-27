<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Tarjetas from './Components/Tarjetas.vue';
import PowerBICharts from '@/Components/Dashboard/PowerBICharts.vue';
</script>

<template>
  <AppLayout title="Métodos y Estadísticas">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Métodos y Estadísticas
      </h2>
    </template>

    <div class="py-12 min-h-screen" :style="{ backgroundColor: 'var(--bg-light)' }">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-6" :style="{ color: 'var(--text-primary)' }">
          <Tarjetas />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import QuickAccessCards from '@/Components/Dashboard/QuickAccessCards.vue';
import PowerBICharts from '@/Components/Dashboard/PowerBICharts.vue';
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">Dashboard</h2>
    </template>

    <div class="py-12" :style="{ backgroundColor: 'var(--bg-light)' }">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Pedidos Button - Mejorado -->
        <div class="mb-8">
          <button
            @click="$inertia.get(route('pedidos.index'))"
            class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-3 transform hover:scale-105"
          >
            <i class="fa-solid fa-clipboard-list text-2xl"></i>
            <span class="text-lg">Gestionar Pedidos</span>
            <i class="fa-solid fa-arrow-right text-xl"></i>
          </button>
        </div>

        <!-- Quick Access Cards -->
        <!-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
          <QuickAccessCards />
        </div> -->

        <!-- Power BI Charts -->
        <div class="mt-8 overflow-hidden shadow-xl sm:rounded-lg p-6" :style="{ backgroundColor: 'var(--bg-light)', color: 'var(--text-primary)' }">
          <PowerBICharts />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

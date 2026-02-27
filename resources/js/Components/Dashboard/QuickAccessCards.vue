<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
    <!-- Tarjeta Caja -->
    <a href="#"
       class="card-hover block bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl shadow-md overflow-hidden border-2 border-blue-400 dark:border-blue-600 hover:shadow-xl transition-all duration-300">
      <div class="p-6 flex flex-col items-center text-center">
        <div class="icon-container mb-5 p-5 rounded-2xl bg-blue-600 dark:bg-blue-500">
          <i class="fas fa-cash-register text-white text-4xl"></i>
        </div>
        <h3 class="text-xl font-bold text-blue-900 dark:text-blue-100 mb-2">Caja</h3>
        <p class="text-blue-700 dark:text-blue-300 mb-4">Realizar ventas, transacciones y gestionar efectivo</p>
        <div class="flex items-center text-blue-800 dark:text-blue-200 font-bold">
          <span>Acceder</span>
          <i class="ml-2 fas fa-arrow-right"></i>
        </div>
      </div>
    </a>
  </div>
</template>

<script setup>
import DashboardCard from './DashboardCard.vue';
</script>

<style scoped>
.card-hover {
  transition: all 0.3s ease;
  transform: translateY(0);
}
.card-hover:hover {
  transform: translateY(-5px);
}
.icon-container {
  transition: all 0.3s ease;
}
.card-hover:hover .icon-container {
  transform: scale(1.1) rotate(5deg);
}
</style>

<script setup>
import { ref } from 'vue';

const cards = ref([
  {
    title: 'PEPS',
    description: 'Primeras Entradas, Primeras Salidas. Ideal para productos perecederos.',
    icon: 'fas fa-sort-amount-down',
    gradient: 'from-emerald-50 to-emerald-100',
    border: 'border-emerald-400',
    route: 'inventario.metodo.peps',
  },
  {
    title: 'UEPS',
    description: 'Últimas Entradas, Primeras Salidas. Útil en tiempos de inflación.',
    icon: 'fas fa-sort-amount-up',
    gradient: 'from-amber-50 to-amber-100',
    border: 'border-amber-400',
    route: 'inventario.metodo.ueps',
  },
  {
    title: 'Promedio Ponderado',
    description: 'Costo promedio por unidad. Suaviza fluctuaciones de precios.',
    icon: 'fas fa-balance-scale',
    gradient: 'from-blue-50 to-blue-100',
    border: 'border-blue-400',
    route: 'inventario.metodo.promedio',
  },
]);
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <a
      v-for="card in cards"
      :key="card.title"
      :href="route(card.route)"
      class="card-hover block rounded-2xl shadow-md overflow-hidden border-2 hover:shadow-xl transition-all duration-300"
      :style="{ background: `linear-gradient(135deg, var(--bg-medium) 0%, rgba(255,255,255,0.02) 100%)`, borderColor: 'var(--border)', color: 'var(--text-primary)' }"
    >
      <div class="p-6 flex flex-col items-center text-center">
        <div class="icon-container mb-5 p-5 rounded-2xl" :style="{ backgroundColor: 'var(--primary)' }">
          <i :class="[card.icon, 'text-white text-4xl']"></i>
        </div>
        <h3 class="text-xl font-bold mb-2" :style="{ color: 'var(--text-primary)' }">{{ card.title }}</h3>
        <p class="mb-4" :style="{ color: 'var(--text-secondary)' }">{{ card.description }}</p>
        <div class="flex items-center font-bold" :style="{ color: 'var(--secondary)' }">
          <span>Ver Reporte</span>
          <i class="ml-2 fas fa-arrow-right"></i>
        </div>
      </div>
    </a>
  </div>
</template>

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

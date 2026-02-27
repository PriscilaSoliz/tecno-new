<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Tarjeta Recetas -->
    <a :href="route('recetas.index')"
       class="card-hover block rounded-2xl shadow-md overflow-hidden border-2 hover:shadow-xl transition-all duration-300"
       :style="{ background: 'linear-gradient(135deg, var(--bg-medium) 0%, rgba(255,255,255,0.02) 100%)', color: 'var(--text-primary)', borderColor: 'var(--border)' }">
      <div class="p-6 flex flex-col items-center text-center">
        <div class="icon-container mb-5 p-5 rounded-2xl" :style="{ backgroundColor: 'var(--secondary)' }">
          <i class="fas fa-utensils text-white text-4xl"></i>
        </div>
        <h3 class="text-xl font-bold" :style="{ color: 'var(--text-primary)' }">Recetas</h3>
        <p class="mb-4" :style="{ color: 'var(--text-secondary)' }">Gestionar recetas y listas de ingredientes para productos terminados.</p>
        <div class="flex items-center text-amber-800 dark:text-amber-200 font-bold">
          <span>Administrar</span>
          <i class="ml-2 fas fa-arrow-right"></i>
        </div>
      </div>
    </a>

    <!-- Tarjeta Productos -->
    <a :href="route('productos.index')"
       class="card-hover block rounded-2xl shadow-md overflow-hidden border-2 hover:shadow-xl transition-all duration-300"
       :style="{ background: 'linear-gradient(135deg, var(--bg-medium) 0%, rgba(255,255,255,0.02) 100%)', color: 'var(--text-primary)', borderColor: 'var(--border)' }">
      <div class="p-6 flex flex-col items-center text-center">
        <div class="icon-container mb-5 p-5 rounded-2xl" :style="{ backgroundColor: 'var(--secondary)' }">
          <i class="fas fa-box text-white text-4xl"></i>
        </div>
        <h3 class="text-xl font-bold" :style="{ color: 'var(--text-primary)' }">Productos</h3>
        <p class="mb-4" :style="{ color: 'var(--text-secondary)' }">Gestionar productos terminados, precios y producción.</p>
        <div class="flex items-center text-blue-800 dark:text-blue-200 font-bold">
          <span>Administrar</span>
          <i class="ml-2 fas fa-arrow-right"></i>
        </div>
      </div>
    </a>

    <!-- Tarjeta Órdenes -->
    <a :href="route('ordenes.index')"
       class="card-hover block rounded-2xl shadow-md overflow-hidden border-2 hover:shadow-xl transition-all duration-300 md:col-span-2"
       :style="{ background: 'linear-gradient(135deg, var(--bg-medium) 0%, rgba(255,255,255,0.02) 100%)', color: 'var(--text-primary)', borderColor: 'var(--border)' }">
      <div class="p-6 flex flex-col items-center text-center">
        <div class="icon-container mb-5 p-5 rounded-2xl" :style="{ backgroundColor: 'var(--secondary)' }">
          <i class="fas fa-clipboard-list text-white text-4xl"></i>
        </div>
        <h3 class="text-xl font-bold" :style="{ color: 'var(--text-primary)' }">Órdenes</h3>
        <p class="mb-4" :style="{ color: 'var(--text-secondary)' }">Gestionar órdenes de producción y seguimiento de pedidos.</p>
        <div class="flex items-center text-green-800 dark:text-green-200 font-bold">
          <span>Administrar</span>
          <i class="ml-2 fas fa-arrow-right"></i>
        </div>
      </div>
    </a>
  </div>
</template>

<script setup>
const props = defineProps({
  recetasCount: { type: Number, default: 30 },
  productosCount: { type: Number, default: 50 },
  enProduccion: { type: Number, default: 4 },
  ordenesPendientes: { type: Number, default: 3 },
  updatedText: { type: String, default: 'hoy' },
});
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

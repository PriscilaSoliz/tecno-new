<script setup>
import { ref, onMounted, computed } from 'vue';
import { fetchTopProducts, fetchSalesTimeline } from '@/Services/DashboardService';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, Title } from 'chart.js';
import { Pie, Line } from 'vue-chartjs';

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, Title);

const topProducts = ref([]);
const salesTimeline = ref([]);
const loading = ref(true);

onMounted(async () => {
  try {
    topProducts.value = await fetchTopProducts();
    salesTimeline.value = await fetchSalesTimeline();
  } catch (error) {
    console.error('Error loading charts:', error);
    // Datos simulados si falla la carga
    topProducts.value = [
      { nombre: 'Pan Francés', total: 150 },
      { nombre: 'Torta Chocolate', total: 120 },
      { nombre: 'Croissant', total: 90 },
      { nombre: 'Baguette', total: 75 },
      { nombre: 'Pan Integral', total: 60 }
    ];
    salesTimeline.value = [
      { date: '2024-11-20', nombre: 'Pan Francés', total: 20 },
      { date: '2024-11-21', nombre: 'Pan Francés', total: 25 },
      // Más datos simulados...
    ];
  } finally {
    loading.value = false;
  }
});

// Configuración del gráfico de pastel
const pieChartData = computed(() => ({
  labels: topProducts.value.map(p => p.nombre),
  datasets: [{
    data: topProducts.value.map(p => p.total),
    backgroundColor: [
      '#10B981', // emerald
      '#F59E0B', // amber
      '#3B82F6', // blue
      '#8B5CF6', // violet
      '#EF4444'  // red
    ],
    borderWidth: 2,
    borderColor: '#FFFFFF'
  }]
}));

const pieChartOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: 'bottom',
    },
    title: {
      display: true,
      text: 'Productos Más Vendidos (Últimos 3 Días)'
    }
  }
};

// Configuración del gráfico de línea
const lineChartData = computed(() => {
  const dates = [...new Set(salesTimeline.value.map(s => s.date))].sort();
  const products = [...new Set(salesTimeline.value.map(s => s.nombre))];
  
  return {
    labels: dates,
    datasets: products.map((product, index) => ({
      label: product,
      data: dates.map(date => {
        const entry = salesTimeline.value.find(s => s.date === date && s.nombre === product);
        return entry ? entry.total : 0;
      }),
      borderColor: ['#10B981', '#F59E0B', '#3B82F6', '#8B5CF6', '#EF4444'][index % 5],
      backgroundColor: ['rgba(16, 185, 129, 0.1)', 'rgba(245, 158, 11, 0.1)', 'rgba(59, 130, 246, 0.1)', 'rgba(139, 92, 246, 0.1)', 'rgba(239, 68, 68, 0.1)'][index % 5],
      tension: 0.4
    }))
  };
});

const lineChartOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: 'top',
    },
    title: {
      display: true,
      text: 'Tendencia de Ventas (Últimos 10 Días)'
    }
  },
  scales: {
    y: {
      beginAtZero: true
    }
  }
};
</script>

<template>
  <div class="space-y-8">
    <!-- Título de la sección de gráficos -->
    <div class="text-center">
      <h2 class="text-3xl font-bold text-gray-800 mb-2">📊 Estadísticas de Ventas</h2>
      <p class="text-gray-600">Análisis de productos más vendidos y tendencias de ventas</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Top Products Chart -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div v-if="loading" class="flex items-center justify-center h-64">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        </div>
        <div v-else-if="topProducts.length === 0" class="flex flex-col items-center justify-center h-64 text-gray-500">
          <i class="fas fa-chart-pie text-6xl mb-4 text-gray-300"></i>
          <p class="text-lg font-medium">No hay datos disponibles</p>
          <p class="text-sm">Registra ventas para ver estadísticas</p>
        </div>
        <Pie v-else :data="pieChartData" :options="pieChartOptions" class="w-full h-64" />
      </div>

      <!-- Sales Timeline Chart -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div v-if="loading" class="flex items-center justify-center h-64">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
        </div>
        <div v-else-if="salesTimeline.length === 0" class="flex flex-col items-center justify-center h-64 text-gray-500">
          <i class="fas fa-chart-line text-6xl mb-4 text-gray-300"></i>
          <p class="text-lg font-medium">No hay datos disponibles</p>
          <p class="text-sm">Registra ventas para ver tendencias</p>
        </div>
        <Line v-else :data="lineChartData" :options="lineChartOptions" class="w-full h-64" />
      </div>
    </div>
  </div>
</template>

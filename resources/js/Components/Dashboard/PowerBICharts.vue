<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { fetchTopProducts, fetchSalesTimeline, fetchPaymentMethods, fetchDailyRevenue } from '@/Services/DashboardService';
import { 
  Chart as ChartJS, 
  ArcElement, 
  Tooltip, 
  Legend, 
  CategoryScale, 
  LinearScale, 
  PointElement, 
  LineElement, 
  BarElement,
  Title,
  Filler
} from 'chart.js';
import { Pie, Line, Bar, Doughnut } from 'vue-chartjs';
import { useAccessibility } from '@/Composables/useAccessibility';

ChartJS.register(
  ArcElement, 
  Tooltip, 
  Legend, 
  CategoryScale, 
  LinearScale, 
  PointElement, 
  LineElement, 
  BarElement,
  Title,
  Filler
);

const { isDark } = useAccessibility();

const topProducts = ref([]);
const salesTimeline = ref([]);
const paymentMethodsData = ref([]);
const dailyRevenueData = ref([]);
const loading = ref(true);

const loadData = async () => {
    loading.value = true;
    const baseUrl = usePage().props.app_url || '';
    try {
        const [products, timeline, methods, revenue] = await Promise.all([
            fetchTopProducts(baseUrl),
            fetchSalesTimeline(baseUrl),
            fetchPaymentMethods(baseUrl),
            fetchDailyRevenue(baseUrl)
        ]);
        topProducts.value = products;
        salesTimeline.value = timeline;
        paymentMethodsData.value = methods;
        dailyRevenueData.value = revenue;
    } catch (error) {
        console.error('Error loading charts:', error);
        // Datos simulados
        topProducts.value = [{ nombre: 'Pan Francés', total: 150 }, { nombre: 'Torta Chocolate', total: 120 }];
        paymentMethodsData.value = [
            { tipo_pago: 'efectivo', count: 45, total_amount: 1500 },
            { tipo_pago: 'tarjeta', count: 20, total_amount: 800 },
            { tipo_pago: 'qr', count: 35, total_amount: 1200 }
        ];
    } finally {
        loading.value = false;
    }
};

onMounted(loadData);

// Configuración de colores según modo oscuro
const textColor = computed(() => isDark.value ? '#F9FAFB' : '#374151');
const gridColor = computed(() => isDark.value ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)');

const commonOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: textColor.value,
                font: { family: "'Inter', sans-serif", size: 12, weight: '500' }
            }
        },
        title: {
            display: true,
            color: textColor.value,
            font: { family: "'Inter', sans-serif", size: 16, weight: '700' }
        }
    },
    scales: {
        x: {
            grid: { color: gridColor.value },
            ticks: { color: textColor.value }
        },
        y: {
            grid: { color: gridColor.value },
            ticks: { color: textColor.value }
        }
    }
}));

// 1. Gráfico de Pastel: Top Productos
const pieChartData = computed(() => ({
    labels: topProducts.value.map(p => p.nombre),
    datasets: [{
        data: topProducts.value.map(p => p.total),
        backgroundColor: ['#10B981', '#F59E0B', '#3B82F6', '#8B5CF6', '#EF4444'],
        hoverOffset: 15,
        borderWidth: 2,
        borderColor: isDark.value ? '#1F2937' : '#FFFFFF'
    }]
}));

const pieOptions = computed(() => ({
    ...commonOptions.value,
    plugins: {
        ...commonOptions.value.plugins,
        title: { ...commonOptions.value.plugins.title, text: 'Top 5 Productos Vendidos' }
    },
    scales: {} // Pie charts don't use scales
}));

// 2. Gráfico de Línea: Tendencia de Ventas
const lineChartData = computed(() => {
    const dates = [...new Set(salesTimeline.value.map(s => s.date))].sort();
    const products = [...new Set(salesTimeline.value.map(s => s.nombre))];
    return {
        labels: dates,
        datasets: products.slice(0, 3).map((product, index) => ({
            label: product,
            data: dates.map(date => {
                const entry = salesTimeline.value.find(s => s.date === date && s.nombre === product);
                return entry ? entry.total : 0;
            }),
            borderColor: ['#10B981', '#3B82F6', '#EF4444'][index % 3],
            backgroundColor: ['rgba(16, 185, 129, 0.1)', 'rgba(59, 130, 246, 0.1)', 'rgba(239, 68, 68, 0.1)'][index % 3],
            fill: true,
            tension: 0.4
        }))
    };
});

// 3. Gráfico de Doughnut: Métodos de Pago
const paymentChartData = computed(() => ({
    labels: paymentMethodsData.value.map(m => m.tipo_pago.toUpperCase()),
    datasets: [{
        data: paymentMethodsData.value.map(m => m.count),
        backgroundColor: ['#10B981', '#3B82F6', '#8B5CF6'],
        borderWidth: 0,
        cutout: '70%',
        borderRadius: 5
    }]
}));

const paymentOptions = computed(() => ({
    ...commonOptions.value,
    plugins: {
        ...commonOptions.value.plugins,
        title: { ...commonOptions.value.plugins.title, text: 'Uso de Métodos de Pago' }
    },
    scales: {}
}));

// 4. Gráfico de Barras: Ingresos Diarios
const revenueChartData = computed(() => ({
    labels: dailyRevenueData.value.map(r => r.date),
    datasets: [{
        label: 'Ingresos (Bs)',
        data: dailyRevenueData.value.map(r => r.total),
        backgroundColor: '#F59E0B',
        borderRadius: 8,
        barThickness: 20
    }]
}));

const revenueOptions = computed(() => ({
    ...commonOptions.value,
    plugins: {
        ...commonOptions.value.plugins,
        title: { ...commonOptions.value.plugins.title, text: 'Ingresos Diarios (30d)' }
    }
}));

</script>

<template>
  <div class="space-y-10">
    <!-- Encabezado con Icono Moderno -->
    <div class="flex flex-col items-center">
      <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl shadow-lg flex items-center justify-center mb-4 transition-transform hover:rotate-12 duration-300">
        <i class="fas fa-chart-line text-white text-3xl"></i>
      </div>
      <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white tracking-tight">Estadísticas Estratégicas</h2>
      <p class="text-gray-500 dark:text-gray-400 max-w-lg text-center mt-2 font-medium">Visualiza el rendimiento real de tu negocio con datos actualizados al instante.</p>
    </div>

    <!-- Grid de Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      
      <!-- 1. Top Productos (Pie) -->
      <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none p-8 border border-white/20 backdrop-blur-sm transition-all hover:shadow-2xl">
        <div v-if="loading" class="flex items-center justify-center h-[300px]">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-500"></div>
        </div>
        <div v-else class="h-[300px] relative">
          <Pie :data="pieChartData" :options="pieOptions" />
        </div>
      </div>

      <!-- 2. Métodos de Pago (Doughnut) -->
      <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none p-8 border border-white/20 backdrop-blur-sm transition-all hover:shadow-2xl">
        <div v-if="loading" class="flex items-center justify-center h-[300px]">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        </div>
        <div v-else class="h-[300px] relative">
          <Doughnut :data="paymentChartData" :options="paymentOptions" />
          <div class="absolute inset-x-0 bottom-4 text-center">
            <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Preferencia Cliente</p>
          </div>
        </div>
      </div>

      <!-- 3. Tendencia (Line) -->
      <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none p-8 border border-white/20 backdrop-blur-sm transition-all hover:shadow-2xl">
        <div v-if="loading" class="flex items-center justify-center h-[350px]">
            <div class="flex gap-2">
                <div class="w-3 h-3 bg-amber-500 rounded-full animate-bounce"></div>
                <div class="w-3 h-3 bg-amber-500 rounded-full animate-bounce [animation-delay:-.3s]"></div>
                <div class="w-3 h-3 bg-amber-500 rounded-full animate-bounce [animation-delay:-.5s]"></div>
            </div>
        </div>
        <div v-else class="h-[350px]">
          <Line :data="lineChartData" :options="commonOptions" />
        </div>
      </div>

      <!-- 4. Ingresos (Bar) - Nuevo -->
      <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none p-8 border border-white/20 backdrop-blur-sm transition-all hover:shadow-2xl">
        <div v-if="loading" class="flex items-center justify-center h-[300px]">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500"></div>
        </div>
        <div v-else class="h-[300px]">
          <Bar :data="revenueChartData" :options="revenueOptions" />
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
/* Transiciones suaves para los gráficos al cambiar el tema */
canvas {
    transition: all 0.5s ease;
}
</style>

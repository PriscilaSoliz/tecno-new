<script setup>
const props = defineProps({ estado: { type: String, default: 'pending' } });

const steps = [
  { key: 'pending', label: 'Pedido recibido', icon: 'fa-clipboard-check' },
  { key: 'assigned', label: 'Preparando / Asignando delivery', icon: 'fa-box' },
  { key: 'on_route', label: 'En camino', icon: 'fa-truck' },
  // Se eliminó el paso 'delivered' (Entregado) según requerimiento
];

const getStepClass = (key) => {
  const order = ['pending', 'assigned', 'on_route', 'delivered', 'completed'];
  const currentIndex = order.indexOf(props.estado || 'pending');
  const stepIndex = order.indexOf(key);
  
  if (stepIndex < currentIndex) return 'bg-green-600 text-white'; // completado
  if (stepIndex === currentIndex) return 'bg-amber-500 text-white animate-pulse'; // actual
  return 'bg-gray-200 text-gray-500'; // pendiente
};
</script>

<template>
  <div class="space-y-3">
    <h4 class="font-bold text-gray-800 mb-4">Estado del pedido</h4>
    <div v-for="(s, index) in steps" :key="s.key" class="flex items-center gap-3">
      <div :class="[getStepClass(s.key), 'w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 z-10']">
        <i :class="['fa-solid', s.icon]"></i>
      </div>
      <div class="flex-1">
        <p :class="['font-medium', getStepClass(s.key).includes('green') || getStepClass(s.key).includes('amber') ? 'text-gray-900' : 'text-gray-400']">
          {{ s.label }}
        </p>
      </div>
      <!-- Línea conectora -->
      <div v-if="index < steps.length - 1" class="absolute left-5 mt-10 w-0.5 h-6 bg-gray-200 -z-0"></div>
    </div>
    
    <!-- Mensaje final si está entregado/completado -->
    <div v-if="props.estado === 'delivered' || props.estado === 'completed'" class="mt-4 p-3 bg-green-50 text-green-800 rounded-lg text-center text-sm font-medium">
        <i class="fas fa-check-circle mr-1"></i> Pedido entregado con éxito
    </div>
  </div>
</template>

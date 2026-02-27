<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
  products: { type: Array, default: () => [] },
  operarios: { type: Array, default: () => [] },
});
const emit = defineEmits(['close','created']);

const form = useForm({
  producto_id: props.products[0]?.id ?? null,
  cantidad_a_producir: 1,
});

const selectedProduct = computed(() => props.products.find(p => p.id === Number(form.producto_id)) || null);

// Simulación: generar asignación por lotes
const allocation = ref([]);

const computeAllocation = () => {
  allocation.value = [];
  if (!selectedProduct.value) return;

  // Las recetas están directamente en el producto (cada receta = un ingrediente)
  const recetas = selectedProduct.value.recetas || [];
  if (!recetas.length) return;

  recetas.forEach(receta => {
    // cant_x_unidad es la cantidad del ingrediente por unidad de producto
    const neededTotal = Number(receta.cant_x_unidad) * Number(form.cantidad_a_producir);
    let remaining = neededTotal;
    const alloc = [];
    
    // lotes provistos desde controller (PEPS asc)
    const lots = receta.lotes || [];
    for (const lote of lots) {
      if (remaining <= 0) break;
      const take = Math.min(lote.cantidad_disponible_x_unidad, remaining);
      if (take > 0) {
        alloc.push({ 
          lote_id: lote.id, 
          qty: take, 
          fecha_ingreso: lote.fecha_ingreso, 
          available: lote.cantidad_disponible_x_unidad 
        });
        remaining -= take;
      }
    }
    
    allocation.value.push({
      ingrediente_id: receta.ingrediente_id,
      ingrediente_nombre: receta.ingrediente?.nombre || 'Ingrediente',
      needed: neededTotal,
      allocated: alloc,
      remaining: remaining,
      available_stock: receta.available_stock ?? 0,
    });
  });
};

// recalcular cuando cambian producto o cantidad
watch(() => [form.producto_id, form.cantidad_a_producir], computeAllocation, { immediate: true });

const submit = () => {
  // Verificar allocation antes de enviar
  const missing = allocation.value.find(a => a.remaining > 0);
  if (missing) {
    alert(`Stock insuficiente para ${missing.ingrediente_nombre}. Faltan ${missing.remaining}.`);
    return;
  }

  form.post(route('ordenes.store'), {
    preserveScroll: true,
    onSuccess: () => {
      emit('created');
      form.reset();
    },
    onError: (errors) => {
      // Mostrar errores del backend
      const errorMsg = errors.error || errors.producto_id || 'Error al crear la orden';
      alert(errorMsg);
    }
  });
};

const close = () => emit('close');
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl w-full max-w-lg shadow-lg max-h-[90vh] overflow-y-auto">
      <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between sticky top-0 bg-white dark:bg-gray-800">
        <h3 class="font-semibold text-gray-900 dark:text-gray-100">Nueva Orden de Producción</h3>
        <button @click="close" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
          <i class="fa-solid fa-times"></i>
        </button>
      </div>

      <form @submit.prevent="submit" class="p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Producto *</label>
          <select v-model="form.producto_id" required class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-white px-3 py-2 rounded focus:ring-2 focus:ring-amber-500">
            <option v-for="p in props.products" :key="p.id" :value="p.id">{{ p.nombre }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Cantidad a producir *</label>
          <input type="number" min="1" v-model="form.cantidad_a_producir" required class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-3 py-2 focus:ring-2 focus:ring-amber-500" />
        </div>

        <!-- Preview de recetas + asignación por lotes -->
        <div v-if="selectedProduct" class="border dark:border-gray-600 rounded p-4 bg-gray-50 dark:bg-gray-700">
          <h4 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">Receta y asignación estimada</h4>
          
          <div v-if="selectedProduct.recetas && selectedProduct.recetas.length">
            <div v-for="a in allocation" :key="a.ingrediente_id" class="mb-4 p-3 bg-white dark:bg-gray-800 rounded border dark:border-gray-600">
              <div class="flex justify-between items-center mb-2">
                <div class="font-medium text-gray-800 dark:text-gray-200">{{ a.ingrediente_nombre }}</div>
                <div class="text-sm">
                  <span class="text-gray-600 dark:text-gray-400">Necesario: <strong>{{ a.needed }}</strong></span>
                  <span class="mx-2">|</span>
                  <span :class="a.available_stock >= a.needed ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                    Disponible: <strong>{{ a.available_stock }}</strong>
                  </span>
                </div>
              </div>

              <ul v-if="a.allocated.length" class="text-sm space-y-1">
                <li v-for="lot in a.allocated" :key="lot.lote_id" class="flex justify-between text-gray-600 dark:text-gray-400">
                  <span>Lote #{{ lot.lote_id }} ({{ lot.fecha_ingreso }})</span>
                  <span class="font-mono text-amber-700 dark:text-amber-400">-{{ lot.qty }}</span>
                </li>
              </ul>

              <div v-if="a.remaining > 0" class="text-rose-600 dark:text-rose-400 text-sm mt-2 font-medium">
                ⚠️ Faltan {{ a.remaining }} unidades
              </div>
            </div>
          </div>
          
          <div v-else class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
            <i class="fa-solid fa-exclamation-triangle text-amber-500 mr-2"></i>
            El producto no tiene receta configurada.
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button type="button" @click="close" class="px-4 py-2 border dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
            Cancelar
          </button>
          <button type="submit" 
                  class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 disabled:opacity-50" 
                  :disabled="form.processing">
            <span v-if="form.processing">Procesando...</span>
            <span v-else>Crear Orden</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

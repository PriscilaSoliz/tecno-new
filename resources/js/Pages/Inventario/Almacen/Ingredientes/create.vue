<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const emit = defineEmits(['close','created']);

const unidades = [
  { abbr: 'kg', label: 'Kilogramo (kg)' },
  { abbr: 'g', label: 'Gramo (g)' },
  { abbr: 'l', label: 'Litro (l)' },
  { abbr: 'ml', label: 'Mililitro (ml)' },
  { abbr: 'pz', label: 'Pieza (pz)' },
  { abbr: 'm', label: 'Metro (m)' },
  { abbr: 'cm', label: 'Centímetro (cm)' },
  { abbr: 'unidad', label: 'Unidad' }
];

// formulario de creación
const form = useForm({
  nombre: '',
  unidad_medida: '',
  descripcion: '',
  is_active: true,
});

const toastMsg = ref('');
const showToast = ref(false);

const submit = () => {
  form.post(route('ingredientes.store'), {
    onSuccess: () => {
      toastMsg.value = '¡Insumo creado exitosamente!';
      showToast.value = true;
      setTimeout(() => {
        showToast.value = false;
        // Redirige al index principal del almacén
        window.location.href = route('almacen.index');
      }, 1200);
      form.reset();
    },
    onError: () => {
      // errors quedan disponibles en form.errors; el modal sigue abierto
    }
  });
};

const close = () => emit('close');
</script>

<template>
  <div class="fixed inset-0 z-50 p-4 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl w-full max-w-xl shadow-xl">
      <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold">Crear Insumo</h3>
        <button @click="close" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
      </div>

      <form @submit.prevent="submit" class="p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
          <input v-model="form.nombre" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-amber-500" />
          <div v-if="form.errors.nombre" class="text-rose-600 text-sm mt-1">{{ form.errors.nombre }}</div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Unidad de medida *</label>
            <select v-model="form.unidad_medida" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-amber-500">
              <option value="" disabled>Selecciona...</option>
              <option v-for="u in unidades" :key="u.abbr" :value="u.abbr">{{ u.label }}</option>
            </select>
            <div v-if="form.errors.unidad_medida" class="text-rose-600 text-sm mt-1">{{ form.errors.unidad_medida }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
            <select v-model="form.is_active" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-amber-500">
              <option :value="true">Activo</option>
              <option :value="false">Inactivo</option>
            </select>
            <div v-if="form.errors.is_active" class="text-rose-600 text-sm mt-1">{{ form.errors.is_active }}</div>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
          <textarea v-model="form.descripcion" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-amber-500"></textarea>
          <div v-if="form.errors.descripcion" class="text-rose-600 text-sm mt-1">{{ form.errors.descripcion }}</div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button type="button" @click="close" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
          <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700" :disabled="form.processing">
            Crear
          </button>
        </div>
      </form>
    </div>
    <!-- Mensaje flotante -->
    <transition name="fade">
      <div v-if="showToast" class="fixed top-8 left-1/2 transform -translate-x-1/2 bg-amber-600 text-white px-6 py-3 rounded shadow-lg z-[9999] font-semibold text-lg">
        {{ toastMsg }}
      </div>
    </transition>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>

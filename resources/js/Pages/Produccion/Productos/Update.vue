<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { watch, ref } from 'vue';
const props = defineProps({
  product: { type: Object, required: true }
});
const emit = defineEmits(['close','updated']);

const form = useForm({
  nombre: props.product.nombre || '',
  unidad_medida: props.product.unidad_medida || '',
  precio_venta: props.product.precio_venta || 0,
  descripcion: props.product.descripcion || '',
  is_active: !!props.product.is_active,
  imagen: null,
  _method: 'PUT', // Para que Laravel reconozca como PUT con FormData
});

// Helper para obtener URL de imagen
const getImageUrl = (img) => {
  if (!img) return null;
  if (typeof img !== 'string') return null;
  if (img.startsWith('http') || img.startsWith('/')) return img;
  return `/${img}`;
};

const imagePreview = ref(getImageUrl(props.product.imagen));
const removeCurrentImage = ref(false);

watch(() => props.product, (p) => {
  form.nombre = p.nombre || '';
  form.unidad_medida = p.unidad_medida || '';
  form.precio_venta = p.precio_venta || 0;
  form.descripcion = p.descripcion || '';
  form.is_active = !!p.is_active;
  form.imagen = null;
  imagePreview.value = getImageUrl(p.imagen);
  removeCurrentImage.value = false;
});

const handleImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.imagen = file;
    removeCurrentImage.value = false;
    // Crear preview
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const removeImage = () => {
  form.imagen = null;
  imagePreview.value = null;
  removeCurrentImage.value = true;
};

const submit = () => {
  // Usamos POST con _method: PUT para FormData
  form.transform((data) => ({
    ...data,
    remove_image: removeCurrentImage.value ? '1' : '0',
  })).post(route('productos.update', props.product.id), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      router.reload({ only: ['productos'] });
      emit('updated');
    }
  });
};

const close = () => emit('close');
</script>

<template>
  <div class="fixed inset-0 z-50 p-4 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl w-full max-w-xl shadow-xl max-h-[90vh] overflow-y-auto">
      <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white">
        <h3 class="text-lg font-semibold">Editar Producto</h3>
        <button @click="close" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
      </div>

      <form @submit.prevent="submit" class="p-6 space-y-4">
        <!-- Imagen -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Imagen del Producto</label>
          <div class="flex items-start gap-4">
            <!-- Preview -->
            <div class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden flex items-center justify-center bg-gray-50">
              <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
              <div v-else class="text-center text-gray-400">
                <i class="fas fa-image text-3xl mb-1"></i>
                <p class="text-xs">Sin imagen</p>
              </div>
            </div>
            <!-- Controles -->
            <div class="flex-1">
              <input 
                type="file" 
                accept="image/*" 
                @change="handleImageChange"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100"
              />
              <p class="mt-1 text-xs text-gray-500">PNG, JPG o WEBP. Máximo 2MB.</p>
              <button 
                v-if="imagePreview" 
                type="button" 
                @click="removeImage" 
                class="mt-2 text-sm text-red-600 hover:text-red-800"
              >
                <i class="fas fa-trash mr-1"></i> Eliminar imagen
              </button>
            </div>
          </div>
          <p v-if="form.errors.imagen" class="text-red-500 text-sm mt-1">{{ form.errors.imagen }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
          <input v-model="form.nombre" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Unidad *</label>
            <input v-model="form.unidad_medida" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Precio *</label>
            <input v-model="form.precio_venta" type="number" step="0.01" min="0" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
          <textarea v-model="form.descripcion" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4">
          <button type="button" @click="close" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50">Cancelar</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" :disabled="form.processing">
            <span v-if="form.processing"><i class="fas fa-spinner fa-spin mr-1"></i> Guardando...</span>
            <span v-else>Guardar</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

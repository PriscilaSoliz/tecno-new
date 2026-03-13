<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ListaProductos from './Components/ListaProductos.vue';
import CreateProductoModal from './Create.vue';
import UpdateProductoModal from './Update.vue';
import { useNotifications } from '@/Composables/useNotifications';

const { confirm, success, error } = useNotifications();

const props = defineProps({
  productos: { type: Array, default: () => [] }
});

const productosLocal = ref((props.productos||[]).map(p => ({ ...p })));
watch(() => props.productos, (n) => productosLocal.value = (n||[]).map(p => ({ ...p })), { immediate: true });

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingProduct = ref(null);
const onCreated = () => {
  showCreateModal.value = false;
  success('Producto creado correctamente', 'Éxito');
};

const openEditModal = (p) => { editingProduct.value = p; showEditModal.value = true; };
const closeEditModal = () => { editingProduct.value = null; showEditModal.value = false; };

const onUpdated = () => {
  showEditModal.value = false;
  success('Producto actualizado correctamente', 'Éxito');
};

const onToggle = async (p) => {
  const newState = !p.is_active;
  const found = productosLocal.value.find(x => x.id === p.id);
  if(found) found.is_active = newState; // optimistic
  try {
    await router.put(route('productos.update', p.id), {
      nombre: p.nombre, unidad_medida: p.unidad_medida, precio_venta: p.precio_venta, descripcion: p.descripcion, is_active: newState
    });
    success(`Producto ${newState ? 'activado' : 'desactivado'}`);
  } catch (e) {
    if(found) found.is_active = !newState;
    error('Ocurrió un error al cambiar el estado', 'Error');
    console.error(e);
  }
};

const onDelete = async (p) => {
  const isConfirmed = await confirm(`¿Eliminar producto "${p.nombre}"? Esta acción es definitiva.`, 'Eliminar Producto');
  if(!isConfirmed) return;
  try {
    await router.delete(route('productos.destroy', p.id), {
      onSuccess: () => { success('Producto eliminado exitosamente', 'Éxito'); }
    });
  } catch(e){ 
      error('Ocurrió un error al eliminar', 'Error');
      console.error(e); 
  }
};
</script>

<template>
  <AppLayout title="Lista de Productos">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
          Lista de Productos
        </h2>
        <button 
          @click="openCreateModal"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors"
        >
          <i class="fas fa-plus mr-2"></i>
          Nuevo Producto
        </button>
      </div>
    </template>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

          <ListaProductos :products="productosLocal" @edit="openEditModal" @toggle="onToggle" @delete="onDelete" />
        </div>
      </div>
    </div>

    <CreateProductoModal v-if="showCreateModal" @close="closeCreateModal" @created="onCreated" />
    <UpdateProductoModal v-if="showEditModal && editingProduct" :product="editingProduct" @close="closeEditModal" @updated="onUpdated" />
  </AppLayout>
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

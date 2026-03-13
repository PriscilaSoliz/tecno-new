<template>
    <!-- Barra de Búsqueda y Filtros -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 w-full relative">
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Buscar recetas..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm sm:text-base"
                >
                <i class="fas fa-search absolute left-3 top-2.5 sm:top-3 text-gray-400"></i>
            </div>
        </div>
    </div>


    <!-- Vista de Tabla (Escritorio) -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                        @click="sortBy('id')"
                    >
                        <div class="flex items-center">
                            ID
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </div>
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                        @click="sortBy('producto')"
                    >
                        <div class="flex items-center">
                            Producto
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </div>
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                        @click="sortBy('ingrediente')"
                    >
                        <div class="flex items-center">
                            Ingrediente
                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                        Cantidad por Unidad
                    </th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr
                    v-for="receta in sortedRecetas"
                    :key="receta.id"
                    class="hover:bg-gray-50 transition-colors"
                >
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        #{{ receta.id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ receta.producto?.name || 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ receta.ingrediente?.name || 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                        <span class="font-black text-amber-600">{{ receta.cant_x_unidad }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex justify-center space-x-3">
                            <button
                                @click="openEditModal(receta)"
                                class="text-blue-600 hover:text-blue-900 transition-all active:scale-95"
                            >
                                <i class="fas fa-edit"></i>
                                Editar
                            </button>
                            <button
                                @click="deleteReceta(receta)"
                                class="text-red-600 hover:text-red-900 transition-all active:scale-95"
                            >
                                <i class="fas fa-trash"></i>
                                Eliminar
                            </button>
                        </div>
                    </td>
                </tr>
                <tr v-if="filteredRecetas.length === 0">
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p>No se encontraron recetas</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Vista de Tarjetas (Móvil) -->
    <div class="lg:hidden p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div v-for="receta in sortedRecetas" :key="'card-' + receta.id"
            class="bg-white dark:bg-gray-800 rounded-3xl p-5 border border-gray-100 dark:border-gray-700 shadow-xl transition-all active:scale-[0.98]">
            
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                    <i class="fas fa-mortar-pestle text-amber-600 text-xl"></i>
                </div>
                <span class="text-[10px] font-black text-gray-400 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded-lg uppercase">ID #{{ receta.id }}</span>
            </div>

            <div class="space-y-4 mb-6">
                <div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">PRODUCTO FINAL</p>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white leading-none truncate">{{ receta.producto?.name || 'N/A' }}</h3>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-4 border border-blue-100/50 dark:border-blue-800/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center">
                            <i class="fas fa-leaf text-blue-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-0.5">INSUMO REQUERIDO</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">{{ receta.ingrediente?.name || 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-0.5">X UNIDAD</p>
                            <p class="text-lg font-black text-blue-800 dark:text-blue-300 tracking-tighter">{{ receta.cant_x_unidad }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="grid grid-cols-2 gap-2 pt-4 border-t border-dashed border-gray-100 dark:border-gray-700">
                <button @click="openEditModal(receta)"
                    class="flex items-center justify-center gap-2 py-3 bg-indigo-50 text-indigo-600 rounded-2xl font-black text-xs uppercase tracking-wider hover:bg-indigo-600 hover:text-white transition-all active:scale-95">
                    <i class="fas fa-edit"></i>
                    Editar
                </button>
                <button @click="deleteReceta(receta)"
                    class="flex items-center justify-center gap-2 py-3 bg-rose-50 text-rose-600 rounded-2xl font-black text-xs uppercase tracking-wider hover:bg-rose-600 hover:text-white transition-all active:scale-95">
                    <i class="fas fa-trash"></i>
                    Borrar
                </button>
            </div>
        </div>

        <div v-if="filteredRecetas.length === 0" class="col-span-full py-16 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-4">
                <i class="fas fa-receipt text-4xl text-gray-200"></i>
            </div>
            <h4 class="text-lg font-black text-gray-400">No hay recetas aquí</h4>
            <p class="text-sm text-gray-300">Intenta cambiar el término de búsqueda.</p>
        </div>
    </div>

    <!-- Modal de Creación -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Nueva Receta</h3>
                <button @click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="createReceta" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Producto *</label>
                    <select v-model="createForm.producto_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        <option value="">Seleccionar Producto</option>
                        <option v-for="prod in productos" :key="prod.id" :value="prod.id">{{ prod.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ingrediente *</label>
                    <select v-model="createForm.ingrediente_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        <option value="">Seleccionar Ingrediente</option>
                        <option v-for="ing in ingredientes" :key="ing.id" :value="ing.id">{{ ing.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad por Unidad *</label>
                    <input type="number" v-model="createForm.cant_x_unidad" required step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" @click="closeCreateModal" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 flex items-center"><i class="fas fa-save mr-2"></i>Crear</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Editar Receta</h3>
                <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form @submit.prevent="updateReceta" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Producto *</label>
                    <select v-model="editForm.producto_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        <option value="">Seleccionar Producto</option>
                        <option v-for="prod in productos" :key="prod.id" :value="prod.id">{{ prod.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ingrediente *</label>
                    <select v-model="editForm.ingrediente_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        <option value="">Seleccionar Ingrediente</option>
                        <option v-for="ing in ingredientes" :key="ing.id" :value="ing.id">{{ ing.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad por Unidad *</label>
                    <input type="number" v-model="editForm.cant_x_unidad" required step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" @click="closeEditModal" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 flex items-center"><i class="fas fa-save mr-2"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useNotifications } from '@/Composables/useNotifications';

const { confirm, success, error } = useNotifications();

const props = defineProps({
    recetas: { type: Array, default: () => [] },
    productos: { type: Array, default: () => [] },
    ingredientes: { type: Array, default: () => [] }
});

const searchQuery = ref('');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const sortField = ref('id');
const sortDirection = ref('desc');

const createForm = ref({ producto_id: '', ingrediente_id: '', cant_x_unidad: '' });
const editForm = ref({ id: null, producto_id: '', ingrediente_id: '', cant_x_unidad: '' });

const filteredRecetas = computed(() => {
    const searchNormalized = (searchQuery.value || '').toLowerCase();
    return props.recetas.filter(r =>
        (r.producto?.name || '').toLowerCase().includes(searchNormalized) ||
        (r.ingrediente?.name || '').toLowerCase().includes(searchNormalized)
    );
});

const sortedRecetas = computed(() => {
    return [...filteredRecetas.value].sort((a, b) => {
        let aValue = a[sortField.value];
        let bValue = b[sortField.value];
        if (typeof aValue === 'string') {
            aValue = aValue.toLowerCase();
            bValue = bValue.toLowerCase();
        }
        if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1;
        if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1;
        return 0;
    });
});

const sortBy = (field) => {
    if (sortField.value === field) sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    else { sortField.value = field; sortDirection.value = 'asc'; }
};

const openEditModal = (receta) => {
    editForm.value = { id: receta.id, producto_id: receta.producto_id, ingrediente_id: receta.ingrediente_id, cant_x_unidad: receta.cant_x_unidad };
    showEditModal.value = true;
};

const closeEditModal = () => { showEditModal.value = false; editForm.value = { id: null, producto_id: '', ingrediente_id: '', cant_x_unidad: '' }; };
const closeCreateModal = () => { showCreateModal.value = false; createForm.value = { producto_id: '', ingrediente_id: '', cant_x_unidad: '' }; };

const createReceta = () => {
    router.post(route('recetas.store'), createForm.value, {
        onSuccess: () => {
            closeCreateModal();
            success('Receta creada exitosamente', 'Éxito');
        },
        onError: (errors) => {
            error('Revisa los datos ingresados', 'Error al crear');
            console.error(errors);
        }
    });
};

const updateReceta = () => {
    router.put(route('recetas.update', editForm.value.id), editForm.value, {
        onSuccess: () => {
            closeEditModal();
            success('Receta actualizada exitosamente', 'Éxito');
        },
        onError: (errors) => {
            error('Revisa los datos ingresados', 'Error al actualizar');
            console.error(errors);
        }
    });
};

const deleteReceta = async (receta) => {
    const isConfirmed = await confirm('¿Eliminar la receta seleccionada de forma permanente?', 'Eliminar Receta');
    if (isConfirmed) {
        router.delete(route('recetas.destroy', receta.id), {
            onSuccess: () => success('Receta eliminada exitosamente', 'Éxito'),
            onError: () => error('No se pudo eliminar la receta', 'Error')
        });
    }
};

// expose a method so parent can open the create modal
const openCreateModal = () => {
    showCreateModal.value = true;
};
defineExpose({ openCreateModal });
</script>

<style scoped>
table { border-spacing: 0; }
th, td { border-bottom: 1px solid #e5e7eb; }
th { background-color: #f9fafb; }
</style>

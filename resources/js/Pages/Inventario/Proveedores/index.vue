<template>
    <AppLayout title="Lista de Proveedores">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Lista de Proveedores
                </h2>
                <button 
                    @click="showCreateModal = true"
                    class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors shadow-sm active:scale-95"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Nuevo Proveedor
                </button>
            </div>
        </template>



        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Barra de Búsqueda y Filtros -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1 w-full relative">
                                <input 
                                    type="text" 
                                    v-model="searchQuery"
                                    placeholder="Buscar proveedores..." 
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base"
                                >
                                <i class="fas fa-search absolute left-3 top-2.5 sm:top-3 text-gray-400"></i>
                            </div>
                            <div class="w-full sm:w-auto">
                                <select v-model="statusFilter" class="w-full sm:w-auto border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 text-sm">
                                    <option value="">Todos los estados</option>
                                    <option value="true">Activos</option>
                                    <option value="false">Inactivos</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Vista de Tabla (Escritorio) -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th 
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors whitespace-nowrap"
                                        @click="sortBy('id')"
                                    >
                                        <div class="flex items-center">
                                            ID
                                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                                        </div>
                                    </th>
                                    <th 
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors whitespace-nowrap"
                                        @click="sortBy('empresa')"
                                    >
                                        <div class="flex items-center">
                                            PROVEEDOR
                                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                                        </div>
                                    </th>
                                    <th 
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors whitespace-nowrap"
                                        @click="sortBy('contacto')"
                                    >
                                        <div class="flex items-center">
                                            CONTACTO
                                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider whitespace-nowrap">
                                        ESTADO
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider whitespace-nowrap font-bold">
                                        ACCIONES
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr 
                                    v-for="proveedor in sortedProveedores" 
                                    :key="proveedor.id"
                                    class="hover:bg-gray-50 transition-colors"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ proveedor.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                                <i class="fas fa-truck-moving text-purple-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ proveedor.empresa }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ proveedor.contacto }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span 
                                            :class="[
                                                'px-2 py-1 text-[10px] font-black uppercase rounded-lg shadow-sm',
                                                proveedor.estado 
                                                    ? 'bg-green-100 text-green-800' 
                                                    : 'bg-red-100 text-red-800'
                                            ]"
                                        >
                                            {{ proveedor.estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex justify-center space-x-3">
                                            <button 
                                                @click="openEditModal(proveedor)"
                                                class="text-blue-600 hover:text-blue-900 transition-all active:scale-95"
                                            >
                                                <i class="fas fa-edit mr-1"></i>
                                                Editar
                                            </button>
                                            <button 
                                                @click="toggleStatus(proveedor)"
                                                :class="[
                                                    'transition-all active:scale-95',
                                                    proveedor.estado 
                                                        ? 'text-orange-600 hover:text-orange-900' 
                                                        : 'text-emerald-600 hover:text-emerald-900'
                                                ]"
                                            >
                                                <i :class="proveedor.estado ? 'fas fa-pause-circle' : 'fas fa-play-circle'" class="mr-1"></i>
                                                {{ proveedor.estado ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredProveedores.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        <i class="fas fa-search text-4xl mb-2 text-gray-300"></i>
                                        <p>No se encontraron proveedores</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Vista de Tarjetas (Móvil) -->
                    <div class="lg:hidden p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="proveedor in sortedProveedores" :key="'card-' + proveedor.id"
                            class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl transition-all active:scale-[0.98]">
                            
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 text-2xl shadow-inner">
                                    <i class="fas fa-building"></i>
                                </div>
                                <span :class="proveedor.estado ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
                                    {{ proveedor.estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>

                            <div class="space-y-4 mb-6">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black text-gray-400">ID #{{ proveedor.id }}</span>
                                    </div>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-1 truncate">{{ proveedor.empresa }}</h3>
                                    <p class="text-xs text-purple-600 dark:text-purple-400 font-bold flex items-center gap-1.5">
                                        <i class="fas fa-id-card text-[10px]"></i>
                                        {{ proveedor.contacto }}
                                    </p>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-dashed border-gray-100 dark:border-gray-700">
                                <button @click="openEditModal(proveedor)"
                                    class="flex items-center justify-center gap-2 py-3 bg-blue-50 text-blue-600 rounded-2xl font-black text-[10px] uppercase tracking-wider hover:bg-blue-600 hover:text-white transition-all active:scale-95 shadow-sm">
                                    <i class="fas fa-edit"></i>
                                    Editar
                                </button>
                                <button @click="toggleStatus(proveedor)"
                                    :class="proveedor.estado ? 'bg-orange-50 text-orange-600 hover:bg-orange-600' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600'"
                                    class="flex items-center justify-center gap-2 py-3 rounded-2xl font-black text-[10px] uppercase tracking-wider hover:text-white transition-all active:scale-95 shadow-sm">
                                    <i :class="proveedor.estado ? 'fas fa-pause' : 'fas fa-play'"></i>
                                    {{ proveedor.estado ? 'Pausar' : 'Activar' }}
                                </button>
                            </div>
                        </div>

                        <div v-if="filteredProveedores.length === 0" class="col-span-full py-16 text-center">
                            <i class="fas fa-folder-open text-5xl text-gray-200 mb-4"></i>
                            <h4 class="text-lg font-black text-gray-400">No hay proveedores</h4>
                            <p class="text-xs text-gray-300 uppercase font-bold tracking-widest">Intenta otra búsqueda</p>
                        </div>
                    </div>

                    <!-- Paginación -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Mostrando {{ filteredProveedores.length }} de {{ proveedoresMapeados.length }} proveedores
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Creación -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Nuevo Proveedor</h3>
                    <button @click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form @submit.prevent="createProveedor" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Empresa *</label>
                        <input 
                            type="text" 
                            v-model="createForm.empresa"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contacto *</label>
                        <input 
                            type="text" 
                            v-model="createForm.contacto"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button 
                            type="button"
                            @click="closeCreateModal"
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center"
                        >
                            <i class="fas fa-save mr-2"></i>
                            Crear Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de Edición -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Editar Proveedor
                    </h3>
                    <button 
                        @click="closeEditModal"
                        class="text-gray-400 hover:text-gray-600 transition-colors"
                    >
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form @submit.prevent="updateProveedor" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre de la Empresa *
                        </label>
                        <input 
                            type="text" 
                            v-model="editForm.empresa"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Contacto *
                        </label>
                        <input 
                            type="text" 
                            v-model="editForm.contacto"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Estado
                        </label>
                        <select 
                            v-model="editForm.estado"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            <option :value="true">Activo</option>
                            <option :value="false">Inactivo</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button 
                            type="button"
                            @click="closeEditModal"
                            class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center"
                        >
                            <i class="fas fa-save mr-2"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useNotifications } from '@/Composables/useNotifications';

const { success, error } = useNotifications();

// Props que vienen del controlador con datos reales
const props = defineProps({
    proveedores: {
        type: Array,
        // datos por defecto que siguen estrictamente la migración (empresa, contacto, estado)
        default: () => ([
            { id: 1, empresa: 'Panadería La Casa', contacto: 'ventas@pancas.com', estado: true },
            { id: 2, empresa: 'Dulces del Barrio', contacto: 'info@dulces.com', estado: false },
            { id: 3, empresa: 'Harina & Amor', contacto: 'hola@harinayamor.com', estado: true },
        ])
    }
});

// Estados reacios y formularios
const searchQuery = ref('');
const statusFilter = ref('');
const showEditModal = ref(false);
const showCreateModal = ref(false);
const sortField = ref('id');
const sortDirection = ref('desc');

const editForm = ref({
    id: null,
    empresa: '',
    contacto: '',
    estado: true
});

const createForm = ref({
    empresa: '',
    contacto: ''
});

// Usamos directamente los datos que vienen de la migración
const proveedoresMapeados = computed(() => {
    return props.proveedores.map(p => ({
        id: p.id,
        empresa: p.empresa,
        contacto: p.contacto || 'No disponible',
        estado: !!p.estado
    }));
});

// Normalizar texto
const normalizeText = (text) => (text || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

// Filtrado (por empresa o contacto) y filtro de estado
const filteredProveedores = computed(() => {
    const searchNormalized = normalizeText(searchQuery.value);

    return proveedoresMapeados.value.filter(proveedor => {
        const empresaNorm = normalizeText(proveedor.empresa);
        const contactoNorm = normalizeText(proveedor.contacto);

        const matchesSearch = empresaNorm.includes(searchNormalized) || contactoNorm.includes(searchNormalized);

        let matchesStatus = true;
        if (statusFilter.value !== '') {
            const required = statusFilter.value === 'true';
            matchesStatus = proveedor.estado === required;
        }

        return matchesSearch && matchesStatus;
    });
});

// Ordenamiento
const sortedProveedores = computed(() => {
    return [...filteredProveedores.value].sort((a, b) => {
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
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
};



const openEditModal = (proveedor) => {
    editForm.value = {
        id: proveedor.id,
        empresa: proveedor.empresa,
        contacto: proveedor.contacto,
        estado: proveedor.estado
    };
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editForm.value = { id: null, empresa: '', contacto: '', estado: true };
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    createForm.value = { empresa: '', contacto: '' };
};

// Crear proveedor -> payload coincide con migración
const createProveedor = async () => {
    try {
        const payload = {
            empresa: createForm.value.empresa || 'Proveedor Ejemplo',
            contacto: createForm.value.contacto || 'contacto@ejemplo.com',
            estado: true
        };

        await router.post(route('proveedores.store'), payload, {
            onSuccess: () => {
                closeCreateModal();
                success('Proveedor creado exitosamente', 'Éxito');
            },
            onError: (errors) => {
                error('Revisa los datos ingresados', 'Error al crear');
                console.error('Error creando proveedor:', errors);
            },
        });
    } catch (e) {
        error('Ocurrió un error inesperado al intentar crear', 'Error');
        console.error('Error creando proveedor:', e);
    }
};

// Actualizar proveedor -> payload coincide con migración
const updateProveedor = async () => {
    try {
        const payload = {
            empresa: editForm.value.empresa || 'Proveedor Actualizado',
            contacto: editForm.value.contacto || 'actualizado@ejemplo.com',
            estado: typeof editForm.value.estado === 'boolean' ? editForm.value.estado : true
        };

        await router.put(route('proveedores.update', editForm.value.id), payload, {
            onSuccess: () => {
                closeEditModal();
                success('Proveedor actualizado correctamente', 'Éxito');
            },
            onError: (errors) => {
                error('Revisa los datos ingresados', 'Error al actualizar');
                console.error('Error actualizando proveedor:', errors);
            },
        });
    } catch (e) {
        error('Ocurrió un error inesperado al intentar actualizar', 'Error');
        console.error('Error actualizando proveedor:', e);
    }
};

const toggleStatus = async (proveedor) => {
    const newStatus = !proveedor.estado;
    try {
        await router.put(route('proveedores.update', proveedor.id), {
            empresa: proveedor.empresa,
            contacto: proveedor.contacto,
            estado: newStatus
        }, {
            onSuccess: () => success(`Proveedor ${newStatus ? 'activado' : 'desactivado'} correctamente`, 'Éxito'),
            onError: (errors) => {
                error('Ocurrió un error al cambiar el estado del proveedor', 'Error');
                console.error('Error cambiando estado:', errors);
            }
        });
    } catch (e) {
        error('Error inesperado cambiando el estado', 'Error');
        console.error('Error cambiando estado:', e);
    }
};
</script>

<style scoped>
table {
    border-spacing: 0;
}

th, td {
    border-bottom: 1px solid #e5e7eb;
}

th {
    background-color: #f9fafb;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
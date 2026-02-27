<template>
    <AppLayout title="Lista de Proveedores">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Lista de Proveedores
                </h2>
                <button 
                    @click="showCreateModal = true"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Nuevo Proveedor
                </button>
            </div>
        </template>

        <!-- Mensaje de Éxito -->
        <div v-if="showSuccessMessage" class="fixed top-4 right-4 z-50">
            <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2 animate-fade-in">
                <i class="fas fa-check-circle"></i>
                <span>{{ successMessage }}</span>
            </div>
        </div>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- Barra de Búsqueda y Filtros -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        v-model="searchQuery"
                                        placeholder="Buscar proveedores..." 
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    >
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <select v-model="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500">
                                    <option value="">Todos los estados</option>
                                    <option value="true">Activos</option>
                                    <option value="false">Inactivos</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Proveedores -->
                    <div class="overflow-x-auto">
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
                                        @click="sortBy('empresa')"
                                    >
                                        <div class="flex items-center">
                                            Proveedor
                                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                                        </div>
                                    </th>
                                    <th 
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                        @click="sortBy('contacto')"
                                    >
                                        <div class="flex items-center">
                                            Contacto
                                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
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
                                            <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-building text-purple-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ proveedor.empresa }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ proveedor.contacto }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            :class="[
                                                'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                                proveedor.estado 
                                                    ? 'bg-green-100 text-green-800' 
                                                    : 'bg-red-100 text-red-800'
                                            ]"
                                        >
                                            {{ proveedor.estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button 
                                                @click="openEditModal(proveedor)"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                            >
                                                <i class="fas fa-edit"></i>
                                                Editar
                                            </button>
                                            <button 
                                                @click="toggleStatus(proveedor)"
                                                :class="[
                                                    'transition-colors',
                                                    proveedor.estado 
                                                        ? 'text-orange-600 hover:text-orange-900' 
                                                        : 'text-green-600 hover:text-green-900'
                                                ]"
                                            >
                                                <i :class="proveedor.estado ? 'fas fa-pause' : 'fas fa-play'"></i>
                                                {{ proveedor.estado ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredProveedores.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                        <p>No se encontraron proveedores</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
const showSuccessMessage = ref(false);
const successMessage = ref('');
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

const showSuccessAlert = (message) => {
    successMessage.value = message;
    showSuccessMessage.value = true;
    setTimeout(() => {
        showSuccessMessage.value = false;
    }, 4000);
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
                showSuccessAlert('Proveedor creado exitosamente');
            },
            onError: (errors) => console.error('Error creando proveedor:', errors),
        });
    } catch (error) {
        console.error('Error creando proveedor:', error);
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
                showSuccessAlert('Proveedor actualizado correctamente');
            },
            onError: (errors) => console.error('Error actualizando proveedor:', errors),
        });
    } catch (error) {
        console.error('Error actualizando proveedor:', error);
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
            onSuccess: () => showSuccessAlert(`Proveedor ${newStatus ? 'activado' : 'desactivado'} correctamente`),
            onError: (errors) => console.error('Error cambiando estado:', errors)
        });
    } catch (error) {
        console.error('Error cambiando estado:', error);
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
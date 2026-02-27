<template>
    <AppLayout title="Gestión de Almacén">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestión de Almacén
                </h2>
                <button @click="openCreateModal"
                   class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Crear Insumo
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <!-- 🔍 Barra de Búsqueda -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row gap-4 items-center">
                            <div class="flex-1">
                                <div class="relative">
                                    <input
                                        type="text"
                                        v-model="searchQuery"
                                        placeholder="Buscar ingredientes (nombre, unidad de medida, descripción)..."
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                                    >
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                            </div>

                            <!-- filtros: reemplazar botones por select -->
                            <div class="flex items-center gap-2">
                                <select v-model="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 text-sm">
                                    <option value="">Todos</option>
                                    <option value="true">Activos</option>
                                    <option value="false">Desactivados</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 📋 Tabla de Ingredientes -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th v-for="col in columnas"
                                        :key="col.field"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors"
                                        @click="sortBy(col.field)">
                                        <div class="flex items-center">
                                            {{ col.label }}
                                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="ingrediente in sortedIngredientes"
                                    :key="ingrediente.id"
                                    class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ ingrediente.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ingrediente.nombre }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ingrediente.unidad_medida }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="max-w-xs truncate">{{ ingrediente.descripcion }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                                'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                                                ingrediente.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                                            ]">
                                            {{ ingrediente.is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center space-x-2">

                                            <!-- Botón Entrada -->
                                            <button
                                                @click="registrarEntrada(ingrediente)"
                                                class="group relative flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 hover:bg-emerald-200 hover:text-emerald-900 transition-all duration-200 shadow-sm"
                                                title="Registrar Entrada">
                                                <i class="fas fa-arrow-down text-lg"></i>
                                                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-20 pointer-events-none">
                                                    Registrar Entrada
                                                </span>
                                            </button>

                                            <!-- Botón Editar -->
                                            <button
                                                @click="editarIngrediente(ingrediente)"
                                                class="group relative flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200 hover:text-blue-900 transition-all duration-200 shadow-sm"
                                                title="Editar Ingrediente">
                                                <i class="fas fa-edit text-lg"></i>
                                                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-20 pointer-events-none">
                                                    Editar
                                                </span>
                                            </button>

                                            <!-- Botón Eliminar -->
                                            <button
                                                @click="eliminarIngrediente(ingrediente)"
                                                class="group relative flex items-center justify-center w-10 h-10 rounded-full bg-red-100 text-red-700 hover:bg-red-200 hover:text-red-900 transition-all duration-200 shadow-sm"
                                                title="Eliminar Ingrediente">
                                                <i class="fas fa-trash text-lg"></i>
                                                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-20 pointer-events-none">
                                                    Eliminar
                                                </span>
                                            </button>

                                        </div>
                                    </td>
                                </tr>

                                <tr v-if="sortedIngredientes.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                        <p>No se encontraron ingredientes</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 📄 Paginación -->
                    <div class="px-6 py-4 border-t border-gray-200 text-sm text-gray-700">
                        Mostrando {{ sortedIngredientes.length }} de {{ ingredientes.length }} ingredientes
                    </div>
                </div>
            </div>
        </div>

        <!-- reemplazamos el modal inline por el componente -->
        <CreateIngredienteModal
            v-if="showCreateModal"
            @close="closeCreateModal"
            @created="onCreated"
        />
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import CreateIngredienteModal from './Ingredientes/create.vue'

const props = defineProps({
    ingredientes: {
        type: Array,
        default: () => ([
            // ejemplos para pruebas rápidas
            { id: 1, nombre: 'Harina 000', unidad_medida: 'kg', descripcion: 'Harina blanca de fuerza', is_active: true },
            { id: 2, nombre: 'Azúcar', unidad_medida: 'kg', descripcion: 'Azúcar refinada', is_active: true },
            { id: 3, nombre: 'Levadura', unidad_medida: 'g', descripcion: 'Levadura seca instantánea', is_active: false },
        ])
    }
})

const ingredientes = computed(() => props.ingredientes || [])

const columnas = [
    { field: 'id', label: 'ID' },
    { field: 'nombre', label: 'Nombre' },
    { field: 'unidad_medida', label: 'Unidad Medida' },
    { field: 'descripcion', label: 'Descripción' },
    { field: 'is_active', label: 'Estado' }
]

// filtros y orden
const searchQuery = ref('')
const sortField = ref('id')
const sortDirection = ref('desc')

// selector estatal (igual que proveedores.index.vue)
const statusFilter = ref('')

// modales + formularios
const showCreateModal = ref(false)
const showEditModal = ref(false)

const editForm = ref({
    id: null,
    nombre: '',
    unidad_medida: '',
    descripcion: '',
    is_active: true
})

// === Métodos de acción (ejemplos) ===
const registrarEntrada = (ingrediente) => {
    router.get(route('almacen.ingredientes.entrada.create', { idIngrediente: ingrediente.id }))
}

const editarIngrediente = (ingrediente) => {
    // abrir modal de edición prefllado
    openEditModal(ingrediente)
}

const eliminarIngrediente = (ingrediente) => {
    if (!confirm(`Eliminar definitivamente "${ingrediente.nombre}"? Esta acción no se puede deshacer.`)) {
        return
    }

    // eliminación permanente (destroy) -> llama al controlador
    router.delete(route('ingredientes.destroy', ingrediente.id), {
        onSuccess: () => {
            // opcional: mostrar notificación o refrescar la lista
            alert('Eliminado correctamente (backend)')
        },
        onError: (e) => {
            console.error('Error eliminando:', e)
        }
    })
}

// === Nuevo: toggle is_active (activar / desactivar) ===
const toggleActive = (ingrediente) => {
    const newState = !ingrediente.is_active
    // Usamos la ruta update para cambiar solo el flag is_active
    router.put(route('ingredientes.update', ingrediente.id), {
        nombre: ingrediente.nombre,
        unidad_medida: ingrediente.unidad_medida,
        descripcion: ingrediente.descripcion,
        is_active: newState
    }, {
        onSuccess: () => {
            // opcional: notificar
        },
        onError: (e) => console.error('Error actualizando is_active', e)
    })
}

// abrir / cerrar modales
const openCreateModal = () => {
    showCreateModal.value = true
}
const closeCreateModal = () => {
    showCreateModal.value = false
}

const openEditModal = (ingrediente) => {
    editForm.value = {
        id: ingrediente.id,
        nombre: ingrediente.nombre,
        unidad_medida: ingrediente.unidad_medida,
        descripcion: ingrediente.descripcion,
        is_active: ingrediente.is_active
    }
    showEditModal.value = true
}

const closeEditModal = () => {
    showEditModal.value = false
    editForm.value = { id: null, nombre: '', unidad_medida: '', descripcion: '', is_active: true }
}

// nuevo handler cuando se crea correctamente
const onCreated = () => {
    // cerrar modal y mostrar notificación mínima
    showCreateModal.value = false
    alert('Insumo creado correctamente')
    // opcional: recargar la página o pedir datos al backend
    // router.reload() // si deseas recargar datos desde el servidor
}

// Crear / Actualizar insumo -> envían payload acorde con la migración
const updateIngrediente = async () => {
    try {
        const payload = {
            nombre: editForm.value.nombre || 'Insumo Actualizado',
            unidad_medida: editForm.value.unidad_medida || 'unidad',
            descripcion: editForm.value.descripcion || '',
            is_active: !!editForm.value.is_active
        }
        await router.put(route('ingredientes.update', editForm.value.id), payload, {
            onSuccess: () => closeEditModal(),
            onError: (errors) => console.error('Error actualizando ingrediente:', errors)
        })
    } catch (err) {
        console.error(err)
    }
}

// === Filtros y ordenamiento ===
const normalizeText = (text) => {
    return (text || '')
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
}

const filteredIngredientes = computed(() => {
    const search = normalizeText(searchQuery.value)
    return ingredientes.value.filter(i => {
        const nombre = normalizeText(i.nombre)
        const unidadMedida = normalizeText(i.unidad_medida)
        const desc = normalizeText(i.descripcion)
        
        const matchesSearch = nombre.includes(search) || unidadMedida.includes(search) || desc.includes(search)

        // aplicar filtro por estado según statusFilter ('' / 'true' / 'false')
        if (statusFilter.value !== '') {
            const required = statusFilter.value === 'true'
            return matchesSearch && (i.is_active === required)
        }

        return matchesSearch
    })
})

const sortedIngredientes = computed(() => {
    return [...filteredIngredientes.value].sort((a, b) => {
        const field = sortField.value
        let aVal = a[field] ?? ''
        let bVal = b[field] ?? ''

        if (typeof aVal === 'string') aVal = aVal.toLowerCase()
        if (typeof bVal === 'string') bVal = bVal.toLowerCase()

        if (aVal < bVal) return sortDirection.value === 'asc' ? -1 : 1
        if (aVal > bVal) return sortDirection.value === 'asc' ? 1 : -1
        return 0
    })
})

const sortBy = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
    } else {
        sortField.value = field
        sortDirection.value = 'asc'
    }
}
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

/* Pequeños estilos nuevos para modales */
.fixed.inset-0.z-50 {
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
}
</style>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    menuItems: Array,
});

const deleteItem = (id) => {
    if (confirm('¿Estás seguro de eliminar este elemento?')) {
        router.delete(route('menu.destroy', id));
    }
};

const normalizeRoles = (roles) => {
    if (!roles || roles.length === 0) return [];
    if (typeof roles === 'string') {
        try { return JSON.parse(roles) ?? []; } catch { return [roles]; }
    }
    return roles;
};
const formatRoles = (roles) => {
    const normalized = normalizeRoles(roles);
    return normalized.length ? normalized : null;
};
</script>

<template>
    <AppLayout title="Gestión de Menú">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Menú
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    
                    <div class="flex justify-end mb-4">
                        <!-- <Link :href="route('menu.create')" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i> Crear Nuevo Item
                        </Link> -->
                    </div>

                    <!-- Leyenda -->
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg text-sm">
                        <p><strong>Menu Padre:</strong> Permite crear submenús. Si un item tiene padre, aparecerá dentro de ese menú como desplegable.</p>
                        <p><strong>Orden:</strong> Número que define la posición del item (1 = primero, 2 = segundo...).</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orden</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="item in menuItems" :key="item.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ item.order }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i v-if="item.icon" :class="item.icon" class="mr-2 text-gray-400"></i>
                                            <span class="font-medium">{{ item.title }}</span>
                                        </div>
                                        <!-- Mostrar hijos si existen -->
                                        <div v-if="item.children && item.children.length" class="ml-4 mt-1 text-sm text-gray-500">
                                            <div v-for="child in item.children" :key="child.id" class="flex items-center mt-1">
                                                <span class="mr-1">↳</span> {{ child.title }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ item.route || '-' }}</code>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-2">
                                            <span
                                                v-if="!formatRoles(item.roles)"
                                                class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-globe mr-1"></i>Público
                                            </span>
                                            <span
                                                v-else
                                                v-for="role in formatRoles(item.roles)"
                                                :key="role"
                                                class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800 capitalize">
                                                {{ role }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span 
                                            :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        >
                                            <i :class="item.is_active ? 'fas fa-check' : 'fas fa-times'" class="mr-1"></i>
                                            {{ item.is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <Link :href="route('menu.edit', item.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            <i class="fas fa-edit"></i> Editar
                                        </Link>
                                        <!-- <button @click="deleteItem(item.id)" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button> -->
                                    </td>
                                </tr>
                                <tr v-if="!menuItems || menuItems.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No hay elementos de menú configurados.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

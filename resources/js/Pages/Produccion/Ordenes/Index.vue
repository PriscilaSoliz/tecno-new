<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Create from './Create.vue';

const props = defineProps({
    ordenes: { type: Array, default: () => [] },
    productos: { type: Array, default: () => [] },
    operarios: { type: Array, default: () => [] },
});

const showCreate = ref(false);

const estadoColor = (estado) => {
    const colores = {
        'pendiente': 'bg-yellow-100 text-yellow-800',
        'en_proceso': 'bg-blue-100 text-blue-800',
        'finalizada': 'bg-green-100 text-green-800',
        'cancelada': 'bg-red-100 text-red-800',
    };
    return colores[estado] || 'bg-gray-100 text-gray-800';
};

const handleCreated = () => {
    showCreate.value = false;
    router.reload();
};

const eliminar = (id) => {
    if (confirm('¿Estás seguro de eliminar esta orden?')) {
        router.delete(route('ordenes.destroy', id));
    }
};
</script>

<template>
    <AppLayout title="Órdenes de Producción">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Órdenes de Producción</h2>
                <button @click="showCreate = true" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    Nueva Orden
                </button>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Tabla de órdenes -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs">
                                    <tr>
                                        <th class="px-6 py-3">ID</th>
                                        <th class="px-6 py-3">Producto</th>
                                        <th class="px-6 py-3">Cantidad</th>
                                        <th class="px-6 py-3">Estado</th>
                                        <th class="px-6 py-3">Operario</th>
                                        <th class="px-6 py-3">Fecha</th>
                                        <th class="px-6 py-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <tr v-for="orden in ordenes" :key="orden.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">#{{ orden.id }}</td>
                                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ orden.producto?.nombre || 'N/A' }}</td>
                                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ orden.cantidad_a_producir }}</td>
                                        <td class="px-6 py-4">
                                            <span :class="estadoColor(orden.estado)" class="px-2 py-1 rounded-full text-xs font-medium">
                                                {{ orden.estado }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                            {{ orden.operario ? `${orden.operario.turno} - ${orden.operario.especialidad}` : 'Sin asignar' }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                            {{ new Date(orden.fecha_creacion).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <button @click="eliminar(orden.id)" class="text-red-600 hover:text-red-800">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="ordenes.length === 0">
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                            <i class="fa-solid fa-inbox text-4xl mb-2 text-gray-300"></i>
                                            <p>No hay órdenes de producción registradas.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Crear -->
        <Create 
            v-if="showCreate" 
            :products="productos" 
            :operarios="operarios"
            @close="showCreate = false"
            @created="handleCreated"
        />
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

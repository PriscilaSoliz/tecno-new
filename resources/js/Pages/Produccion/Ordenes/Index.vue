<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Create from './Create.vue';
import { useNotifications } from '@/Composables/useNotifications';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const roles = computed(() => page.props.auth?.user?.roles || []);
const isProduccionOnly = computed(() => {
    return roles.value.includes('produccion') && !roles.value.includes('propietario');
});

const { confirm, success, error } = useNotifications();

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

const eliminar = async (id) => {
    const isConfirmed = await confirm('¿Estás seguro de eliminar esta orden?', 'Eliminar Orden');
    if (isConfirmed) {
        router.delete(route('ordenes.destroy', id), {
            onSuccess: () => {
                success('Orden eliminada correctamente');
            }
        });
    }
};

const finalizarOrden = async (id) => {
    const isConfirmed = await confirm('¿Estás seguro de marcar esta orden como finalizada? Esto registrará el producto en el stock disponible para la venta.', 'Finalizar Orden');
    if (isConfirmed) {
        router.put(route('ordenes.update', id), {
            action: 'finalizar'
        }, {
            onSuccess: () => {
                success('Orden finalizada con éxito. Producto ingresado al inventario.');
            },
            onError: (errors) => {
                error(errors.error || 'Ocurrió un error al procesar la solicitud.');
            }
        });
    }
};
</script>

<template>
    <AppLayout title="Órdenes de Producción">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Órdenes de Producción</h2>
                <button v-if="!isProduccionOnly" @click="showCreate = true" class="w-full sm:w-auto px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 flex items-center justify-center gap-2 transition-all active:scale-95 shadow-sm">
                    <i class="fa-solid fa-plus"></i>
                    Nueva Orden
                </button>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-4 md:p-6">
                        <!-- Vista de Tabla (Escritorio) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs">
                                    <tr>
                                        <th class="px-6 py-3 whitespace-nowrap">ID</th>
                                        <th class="px-6 py-3 whitespace-nowrap">Producto</th>
                                        <th class="px-6 py-3 whitespace-nowrap">Cantidad</th>
                                        <th class="px-6 py-3 whitespace-nowrap">Estado</th>
                                        <th class="px-6 py-3 whitespace-nowrap">Operario</th>
                                        <th class="px-6 py-3 whitespace-nowrap">Fecha</th>
                                        <th class="px-6 py-3 whitespace-nowrap text-center">Acciones</th>
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
                                            <div v-if="orden.operario">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ orden.operario.user?.name || 'Operario' }}
                                                </div>
                                                <div class="text-xs">
                                                    {{ orden.operario.turno }} - {{ orden.operario.especialidad }}
                                                </div>
                                            </div>
                                            <span v-else>Sin asignar</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                            {{ new Date(orden.fecha_creacion).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center items-center gap-2">
                                                <button v-if="orden.estado === 'en_proceso'" @click="finalizarOrden(orden.id)" title="Finalizar e Ingresar a Stock" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md focus:outline-none text-xs">
                                                    <i class="fa-solid fa-check"></i>
                                                    Finalizar Prod.
                                                </button>
                                                <button v-if="!isProduccionOnly" @click="eliminar(orden.id)" title="Eliminar Orden" class="text-red-600 hover:text-red-800 focus:outline-none">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
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

                        <!-- Vista de Tarjetas (Móvil) -->
                        <div class="md:hidden grid grid-cols-1 gap-4">
                            <div v-for="orden in ordenes" :key="'card-' + orden.id" 
                                class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-100 dark:border-gray-600 shadow-sm transition-all active:scale-[0.98]">
                                
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID #{{ orden.id }}</span>
                                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ orden.producto?.nombre || 'Producto' }}</h3>
                                    </div>
                                    <span :class="estadoColor(orden.estado)" class="px-2 py-1 rounded-lg text-[10px] font-black uppercase">
                                        {{ orden.estado }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-200 dark:border-gray-600">
                                        <p class="text-[9px] text-gray-500 uppercase font-black mb-1">CANTIDAD</p>
                                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ orden.cantidad_a_producir }} ud.</p>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-200 dark:border-gray-600">
                                        <p class="text-[9px] text-gray-500 uppercase font-black mb-1">OPERARIO</p>
                                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300 truncate">
                                            {{ orden.operario?.user?.name || 'Siguen asignar' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-3 border-t border-dashed border-gray-200 dark:border-gray-600">
                                    <div class="text-[10px] text-gray-500 font-medium">
                                        <i class="fa-regular fa-calendar mr-1 text-amber-500"></i>
                                        {{ new Date(orden.fecha_creacion).toLocaleDateString() }}
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <button v-if="orden.estado === 'en_proceso'" @click="finalizarOrden(orden.id)"
                                            class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm active:scale-95 transition-all">
                                            Finalizar
                                        </button>
                                        <button v-if="!isProduccionOnly" @click="eliminar(orden.id)"
                                            class="p-2 bg-rose-50 dark:bg-rose-900/20 text-rose-500 rounded-lg active:scale-95 transition-all">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="ordenes.length === 0" class="py-10 text-center text-gray-500">
                                <i class="fa-solid fa-clipboard-list text-4xl mb-3 opacity-20"></i>
                                <p>No hay órdenes registradas.</p>
                            </div>
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

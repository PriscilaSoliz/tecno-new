<script setup>
import { ref, watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

// Eliminamos import Modal para evitar problemas
// import Modal from '@/Components/Modal.vue'; 

const props = defineProps({
    product: Object,
    show: Boolean
});

const emit = defineEmits(['close', 'updated']);

// Inicializar form con datos de la promo si existe, o defaults
const form = useForm({
    producto_id: props.product.id,
    precio: props.product.promocion?.precio || '',
    fecha_inicio: props.product.promocion?.fecha_inicio || new Date().toISOString().split('T')[0],
    fecha_fin: props.product.promocion?.fecha_fin || '',
    is_active: props.product.promocion?.is_active ?? true,
    promo_id: props.product.promocion?.id || null
});

// Watch para resetear el form cuando cambia el producto o se abre
watch(() => props.product, (newVal) => {
    form.producto_id = newVal.id;
    if (newVal.promocion) {
        form.precio = newVal.promocion.precio;
        form.fecha_inicio = newVal.promocion.fecha_inicio;
        form.fecha_fin = newVal.promocion.fecha_fin;
        form.is_active = Boolean(newVal.promocion.is_active);
        form.promo_id = newVal.promocion.id;
    } else {
        form.precio = '';
        form.fecha_inicio = new Date().toISOString().split('T')[0];
        form.fecha_fin = '';
        form.is_active = true;
        form.promo_id = null;
    }
}, { immediate: true });

const submit = () => {
    if (form.promo_id) {
        form.put(route('promociones.update', form.promo_id), {
            onSuccess: () => {
                emit('updated');
                emit('close');
            }
        });
    } else {
        form.post(route('promociones.store'), {
            onSuccess: () => {
                emit('updated');
                emit('close');
            }
        });
    }
};

const deletePromo = () => {
    if (!confirm('¿Estás seguro de eliminar esta promoción?')) return;

    form.delete(route('promociones.destroy', form.promo_id), {
        onSuccess: () => {
            emit('updated');
            emit('close');
        }
    });
};

const close = () => emit('close');
</script>

<template>
    <!-- Usamos estructura manual de Modal igual que Create.vue -->
    <div v-if="show" class="fixed inset-0 z-50 p-4 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-2xl w-full max-w-xl shadow-xl max-h-[90vh] overflow-y-auto">

            <!-- Header -->
            <div class="px-6 py-4 border-b flex justify-between items-center sticky top-0 bg-white">
                <h2 class="text-lg font-semibold text-gray-900">
                    Administrar Promoción
                </h2>
                <button @click="close" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6">
                <!-- Info Producto -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg flex items-center gap-4">
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Producto</p>
                        <p class="font-medium text-gray-900 text-lg">{{ product.nombre }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Precio Actual</p>
                        <p class="font-bold text-gray-900 text-lg">Bs {{ parseFloat(product.precio_venta).toFixed(2) }}
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submit" class="space-y-4">

                    <!-- Precio Promo -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1">Precio de Oferta (Bs) *</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Bs</span>
                            </div>
                            <input v-model="form.precio" type="number" step="0.01"
                                class="pl-10 block w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="0.00" required />
                        </div>
                        <div v-if="form.errors.precio" class="text-red-500 text-xs mt-1">{{ form.errors.precio }}</div>
                    </div>

                    <!-- Fechas -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1">Fecha Inicio *</label>
                            <input v-model="form.fecha_inicio" type="date"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full"
                                required />
                            <div v-if="form.errors.fecha_inicio" class="text-red-500 text-xs mt-1">{{
                                form.errors.fecha_inicio }}</div>
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700 mb-1">Fecha Fin *</label>
                            <input v-model="form.fecha_fin" type="date"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full"
                                required />
                            <div v-if="form.errors.fecha_fin" class="text-red-500 text-xs mt-1">{{ form.errors.fecha_fin
                                }}</div>
                        </div>
                    </div>

                    <!-- Active Toggle -->
                    <div
                        class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div>
                            <span class="block text-sm font-medium text-gray-900">Activar Promoción</span>
                            <span class="block text-xs text-gray-500">Si está desactivada, no se mostrará en el
                                catálogo.</span>
                        </div>
                        <button type="button" @click="form.is_active = !form.is_active" :class="[
                            'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                            form.is_active ? 'bg-indigo-600' : 'bg-gray-200'
                        ]">
                            <span :class="[
                                'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                                form.is_active ? 'translate-x-5' : 'translate-x-0'
                            ]" />
                        </button>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 flex justify-between items-center pt-4 border-t">
                        <div>
                            <button v-if="form.promo_id" type="button" @click="deletePromo"
                                class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center">
                                <i class="fas fa-trash-alt mr-1"></i> Eliminar
                            </button>
                        </div>

                        <div class="flex space-x-3">
                            <button type="button" @click="close"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
                                Cancelar
                            </button>

                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                :disabled="form.processing">
                                <i v-if="form.processing" class="fas fa-spinner fa-spin mr-1"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</template>

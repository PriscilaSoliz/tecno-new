<template>
    <AppLayout :title="`Registrar Entrada de: ${ingrediente.nombre}`">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <a :href="route('almacen.index')" class="text-amber-600 hover:text-amber-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                    </a>
                    Registrar Entrada de Insumo: <span class="text-amber-600">{{ ingrediente.nombre }}</span>
                </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Proveedor -->
                        <div>
                            <label for="proveedor" class="block text-sm font-medium text-gray-700 mb-1">
                                Proveedor *
                            </label>
                            <select 
                                id="proveedor" 
                                v-model="form.proveedor_id" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                            >
                                <option value="" disabled>Seleccione un proveedor</option>
                                <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor.id">
                                    {{ proveedor.empresa }}
                                </option>
                            </select>
                            <p v-if="form.errors.proveedor_id" class="text-red-500 text-xs mt-1">{{ form.errors.proveedor_id }}</p>
                        </div>

                        <!-- Cantidad a Comprar -->
                        <div>
                            <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1">
                                Cantidad a Comprar ({{ ingrediente.unidad_medida }}) *
                            </label>
                            <input 
                                id="cantidad"
                                type="number" 
                                v-model.number="form.cantidad_total_x_unidad"
                                required
                                min="0.01"
                                step="0.01"
                                placeholder="Ej: 50.5"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                            >
                            <p v-if="form.errors.cantidad_total_x_unidad" class="text-red-500 text-xs mt-1">{{ form.errors.cantidad_total_x_unidad }}</p>
                        </div>

                        <!-- Costo del Lote -->
                        <div>
                            <label for="costo_lote" class="block text-sm font-medium text-gray-700 mb-1">
                                Costo del Lote (Bs.) *
                            </label>
                            <input 
                                id="costo_lote"
                                type="number" 
                                v-model.number="form.costo_lote"
                                required
                                min="0.01"
                                step="0.01"
                                placeholder="Ej: 250.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                            >
                             <p v-if="form.errors.costo_lote" class="text-red-500 text-xs mt-1">{{ form.errors.costo_lote }}</p>
                        </div>

                        <!-- Costo Unitario (Calculado) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Costo Unitario (Bs. por {{ ingrediente.unidad_medida }})
                            </label>
                            <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-600">
                                {{ costoUnitarioCalculado }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Este valor se calcula automáticamente (Costo del Lote / Cantidad).</p>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex justify-end space-x-4 pt-4">
                            <button 
                                type="button"
                                @click="cancelar"
                                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                                :disabled="form.processing"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center"
                                :disabled="form.processing"
                            >
                                <i class="fas fa-save mr-2"></i>
                                Registrar Entrada
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    ingrediente: {
        type: Object,
        required: true,
    },
    proveedores: {
        type: Array,
        required: true,
    }
});

const form = useForm({
    ingrediente_id: props.ingrediente.id,
    proveedor_id: '',
    cantidad_total_x_unidad: null,
    costo_lote: null,
    costo_unitario: 0,
});

const costoUnitarioCalculado = computed(() => {
    if (form.costo_lote > 0 && form.cantidad_total_x_unidad > 0) {
        const costo = (form.costo_lote / form.cantidad_total_x_unidad).toFixed(2);
        form.costo_unitario = parseFloat(costo); // Actualiza el valor en el formulario
        return costo;
    }
    return '0.00';
});

const submit = () => {
    form.post(route('entrada.store'), {
        onSuccess: () => {
            // Podrías mostrar un mensaje de éxito si lo deseas
            // y luego redirigir.
        },
        onError: (errors) => {
            console.error('Error al registrar la entrada:', errors);
        }
    });
};

const cancelar = () => {
    // window.history.back() es una opción, pero es mejor ser explícito.
    router.get(route('almacen.index'));
};

</script>

<style scoped>
/* Puedes agregar estilos específicos aquí si es necesario */
</style>
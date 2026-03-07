<script setup>
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    show: Boolean,
    ingrediente: {
        type: Object,
        default: () => ({ id: null, nombre: '', unidad_medida: '' })
    },
    proveedores: {
        type: Array,
        required: true,
    }
});

const emit = defineEmits(['close', 'success']);

const form = useForm({
    ingrediente_id: null,
    proveedor_id: '',
    cantidad_total_x_unidad: null,
    costo_lote: null,
    costo_unitario: 0,
});

watch(() => props.show, (newVal) => {
    if (newVal && props.ingrediente) {
        form.ingrediente_id = props.ingrediente.id;
        form.proveedor_id = '';
        form.cantidad_total_x_unidad = null;
        form.costo_lote = null;
        form.costo_unitario = 0;
    }
});

const costoUnitarioCalculado = computed(() => {
    if (form.costo_lote > 0 && form.cantidad_total_x_unidad > 0) {
        const costo = (form.costo_lote / form.cantidad_total_x_unidad).toFixed(2);
        form.costo_unitario = parseFloat(costo);
        return costo;
    }
    return '0.00';
});

const submit = () => {
    form.post(route('entrada.store'), {
        onSuccess: () => {
            emit('success', `Se registró la entrada de: ${props.ingrediente.nombre}`);
            close();
        },
        onError: (errors) => {
            console.error('Error al registrar la entrada:', errors);
        }
    });
};

const close = () => {
    form.reset();
    emit('close');
};
</script>

<template>
    <DialogModal :show="show" @close="close">
        <template #title>
            Registrar Entrada de Insumo: <span class="text-amber-600">{{ ingrediente?.nombre }}</span>
        </template>

        <template #content>
            <div class="space-y-6">
                <!-- Proveedor -->
                <div>
                    <InputLabel for="proveedor_modal" value="Proveedor *" />
                    <select 
                        id="proveedor_modal" 
                        v-model="form.proveedor_id" 
                        required
                        class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                    >
                        <option value="" disabled>Seleccione un proveedor</option>
                        <option v-for="proveedor in proveedores" :key="proveedor.id" :value="proveedor.id">
                            {{ proveedor.empresa }}
                        </option>
                    </select>
                    <InputError :message="form.errors.proveedor_id" class="mt-2" />
                </div>

                <!-- Cantidad a Comprar -->
                <div>
                    <InputLabel for="cantidad_modal" :value="`Cantidad a Comprar (${ingrediente?.unidad_medida || '...'}) *`" />
                    <TextInput 
                        id="cantidad_modal"
                        type="number" 
                        v-model.number="form.cantidad_total_x_unidad"
                        required
                        min="0.01"
                        step="0.01"
                        placeholder="Ej: 50.5"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.cantidad_total_x_unidad" class="mt-2" />
                </div>

                <!-- Costo del Lote -->
                <div>
                    <InputLabel for="costo_lote_modal" value="Costo del Lote (Bs.) *" />
                    <TextInput 
                        id="costo_lote_modal"
                        type="number" 
                        v-model.number="form.costo_lote"
                        required
                        min="0.01"
                        step="0.01"
                        placeholder="Ej: 250.00"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.costo_lote" class="mt-2" />
                </div>

                <!-- Costo Unitario (Calculado) -->
                <div>
                    <InputLabel value="Costo Unitario (Bs. por unidad)" />
                    <div class="mt-1 w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-600 font-medium">
                        {{ costoUnitarioCalculado }} Bs.
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Este valor se calcula automáticamente (Costo del Lote / Cantidad).</p>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex space-x-3">
                <SecondaryButton @click="close">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton 
                    class="bg-amber-600 hover:bg-amber-700 active:bg-amber-800"
                    :class="{ 'opacity-25': form.processing }" 
                    :disabled="form.processing"
                    @click="submit"
                >
                    <i class="fas fa-save mr-2"></i>
                    Registrar Entrada
                </PrimaryButton>
            </div>
        </template>
    </DialogModal>
</template>

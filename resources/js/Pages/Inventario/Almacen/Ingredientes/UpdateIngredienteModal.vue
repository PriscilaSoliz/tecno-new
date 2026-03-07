<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
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
        default: () => ({ id: null, nombre: '', unidad_medida: '', descripcion: '', is_active: true })
    }
});

const emit = defineEmits(['close', 'success']);

const unidades = [
  { abbr: 'kg', label: 'Kilogramo (kg)' },
  { abbr: 'g', label: 'Gramo (g)' },
  { abbr: 'l', label: 'Litro (l)' },
  { abbr: 'ml', label: 'Mililitro (ml)' },
  { abbr: 'pz', label: 'Pieza (pz)' },
  { abbr: 'm', label: 'Metro (m)' },
  { abbr: 'cm', label: 'Centímetro (cm)' },
  { abbr: 'unidad', label: 'Unidad' }
];

const form = useForm({
    id: null,
    nombre: '',
    unidad_medida: '',
    descripcion: '',
    is_active: true,
});

watch(() => props.show, (newVal) => {
    if (newVal && props.ingrediente) {
        form.id = props.ingrediente.id;
        form.nombre = props.ingrediente.nombre;
        form.unidad_medida = props.ingrediente.unidad_medida;
        form.descripcion = props.ingrediente.descripcion;
        form.is_active = props.ingrediente.is_active;
    }
});

const submit = () => {
    form.put(route('almacen.ingredientes.update', form.id), {
        onSuccess: () => {
            emit('success', `Insumo "${form.nombre}" actualizado correctamente`);
            close();
        },
        onError: (errors) => {
            console.error('Error al actualizar el insumo:', errors);
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
            Editar Insumo: <span class="text-amber-600">{{ ingrediente?.nombre }}</span>
        </template>

        <template #content>
            <div class="space-y-6">
                <!-- Nombre -->
                <div>
                    <InputLabel for="nombre_edit" value="Nombre *" />
                    <TextInput 
                        id="nombre_edit"
                        type="text" 
                        v-model="form.nombre" 
                        required
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.nombre" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Unidad de Medida -->
                    <div>
                        <InputLabel for="unidad_edit" value="Unidad de Medida *" />
                        <select 
                            id="unidad_edit" 
                            v-model="form.unidad_medida" 
                            required
                            class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                        >
                            <option value="" disabled>Seleccione...</option>
                            <option v-for="u in unidades" :key="u.abbr" :value="u.abbr">
                                {{ u.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.unidad_medida" class="mt-2" />
                    </div>

                    <!-- Estado -->
                    <div>
                        <InputLabel for="estado_edit" value="Estado" />
                        <select 
                            id="estado_edit" 
                            v-model="form.is_active" 
                            class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all"
                        >
                            <option :value="true">Activo</option>
                            <option :value="false">Inactivo</option>
                        </select>
                        <InputError :message="form.errors.is_active" class="mt-2" />
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <InputLabel for="descripcion_edit" value="Descripción" />
                    <textarea 
                        id="descripcion_edit"
                        v-model="form.descripcion"
                        rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-amber-500 focus:ring-amber-500 transition-all"
                    ></textarea>
                    <InputError :message="form.errors.descripcion" class="mt-2" />
                </div>
            </div>
        </template>

        <template #footer>
            <div class="flex space-x-3">
                <SecondaryButton @click="close">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton 
                    class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800"
                    :class="{ 'opacity-25': form.processing }" 
                    :disabled="form.processing"
                    @click="submit"
                >
                    <i class="fas fa-save mr-2"></i>
                    Actualizar
                </PrimaryButton>
            </div>
        </template>
    </DialogModal>
</template>

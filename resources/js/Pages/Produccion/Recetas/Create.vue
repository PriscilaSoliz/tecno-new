<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    productos: { type: Array, default: () => [] },
    ingredientes: { type: Array, default: () => [] }
});

const form = ref({ producto_id: '', ingrediente_id: '', cant_x_unidad: '' });

const submit = () => {
    router.post(route('recetas.store'), form.value, {
        onSuccess: () => router.visit(route('recetas.index')),
        onError: (errors) => console.error(errors)
    });
};
</script>

<template>
    <AppLayout title="Crear Receta">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear Nueva Receta
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Producto *</label>
                            <select v-model="form.producto_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Seleccionar Producto</option>
                                <option v-for="prod in productos" :key="prod.id" :value="prod.id">{{ prod.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ingrediente *</label>
                            <select v-model="form.ingrediente_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Seleccionar Ingrediente</option>
                                <option v-for="ing in ingredientes" :key="ing.id" :value="ing.id">{{ ing.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cantidad por Unidad *</label>
                            <input type="number" v-model="form.cant_x_unidad" required step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <a :href="route('recetas.index')" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">Cancelar</a>
                            <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 flex items-center"><i class="fas fa-save mr-2"></i>Crear Receta</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    parents: Array,
    availableRoles: Array,
});

const form = useForm({
    title: '',
    route: '',
    icon: '',
    parent_id: null,
    order: 0,
    is_active: true,
    roles: [],
});

const submit = () => {
    form.post(route('menu.store'));
};
</script>

<template>
    <AppLayout title="Crear Item de Menú">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear Nuevo Item de Menú
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Título -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Título</label>
                                <input v-model="form.title" type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" />
                                <div v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</div>
                            </div>

                            <!-- Ruta -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Ruta (Laravel route name)</label>
                                <input v-model="form.route" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" />
                                <div v-if="form.errors.route" class="text-red-600 text-sm mt-1">{{ form.errors.route }}</div>
                            </div>

                            <!-- Icono -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Icono (Font Awesome class)</label>
                                <input v-model="form.icon" type="text" placeholder="fas fa-home" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" />
                                <div v-if="form.errors.icon" class="text-red-600 text-sm mt-1">{{ form.errors.icon }}</div>
                            </div>

                            <!-- Parent -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Menú Padre (opcional)</label>
                                <select v-model="form.parent_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option :value="null">-- Sin padre (nivel raíz) --</option>
                                    <option v-for="parent in parents" :key="parent.id" :value="parent.id">{{ parent.title }}</option>
                                </select>
                            </div>

                            <!-- Orden -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Orden</label>
                                <input v-model.number="form.order" type="number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500" />
                            </div>

                            <!-- Roles -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Roles con acceso</label>
                                <div class="space-y-2">
                                    <div v-for="role in availableRoles" :key="role" class="flex items-center">
                                        <input :id="`role-${role}`" v-model="form.roles" :value="role" type="checkbox" class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-500 focus:ring-amber-500" />
                                        <label :for="`role-${role}`" class="ml-2 text-sm text-gray-700 capitalize">{{ role }}</label>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">Si no seleccionas ningún rol, será público para todos</p>
                                </div>
                            </div>

                            <!-- Activo -->
                            <div class="flex items-center">
                                <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded border-gray-300 text-amber-600 shadow-sm focus:border-amber-500 focus:ring-amber-500" />
                                <label for="is_active" class="ml-2 text-sm text-gray-700">Activo</label>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end space-x-3">
                                <a :href="route('menu.index')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancelar</a>
                                <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700 disabled:opacity-50">
                                    Crear
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

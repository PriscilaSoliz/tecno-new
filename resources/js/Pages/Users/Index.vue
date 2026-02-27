<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
    users: Object,
    roles: Array,
});

// Filtrar roles para excluir 'encargadoalmacen'
const filteredRoles = computed(() => {
    return props.roles.filter(role => role.name !== 'encargadoalmacen');
});

const showingModal = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    role: '',
    password: '',
});

const openModal = (user = null) => {
    editingUser.value = user;
    if (user) {
        form.name = user.name;
        form.email = user.email;
        // Asumiendo que el usuario tiene un rol asignado, tomamos el primero
        form.role = user.roles && user.roles.length ? user.roles[0].name : '';
        form.password = '';
    } else {
        form.reset();
        if (props.roles.length > 0) {
            form.role = props.roles[0].name;
        }
    }
    showingModal.value = true;
};

const closeModal = () => {
    showingModal.value = false;
    form.reset();
    editingUser.value = null;
    form.clearErrors();
};

const save = () => {
    if (editingUser.value) {
        form.put(route('users.update', editingUser.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteUser = (user) => {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        router.delete(route('users.destroy', user.id));
    }
};
</script>

<template>
    <AppLayout title="Gestión de Usuarios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Gestión de Usuarios
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">

                    <!-- Header Actions -->
                    <div class="flex justify-between items-center mb-6">
                        <div class="text-gray-600 dark:text-gray-400">
                            Administra los usuarios y sus roles.
                        </div>
                        <PrimaryButton @click="openModal(null)"
                            class="bg-green-600 hover:bg-green-700 active:bg-green-800">
                            <i class="fas fa-plus mr-2"></i> Nuevo Usuario
                        </PrimaryButton>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Correo
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Rol
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="user in users.data" :key="user.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ user.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ user.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-for="role in user.roles" :key="role.id"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-1">
                                            {{ role.name }}
                                        </span>
                                        <span v-if="!user.roles || user.roles.length === 0"
                                            class="text-xs text-gray-400 italic">
                                            Sin rol
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="openModal(user)"
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3"
                                            title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="deleteUser(user)"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                            title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No se encontraron usuarios.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4" v-if="users.links && users.links.length > 3">
                        <div class="flex flex-wrap -mb-1">
                            <template v-for="(link, k) in users.links" :key="k">
                                <div v-if="link.url === null"
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                                    v-html="link.label" />
                                <a v-else
                                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500"
                                    :class="{ 'bg-blue-700 text-white': link.active }" :href="link.url"
                                    v-html="link.label" />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <DialogModal :show="showingModal" @close="closeModal">
            <template #title>
                {{ editingUser ? 'Editar Usuario' : 'Nuevo Usuario' }}
            </template>

            <template #content>
                <div class="grid grid-cols-1 gap-6">
                    <!-- Name -->
                    <div>
                        <InputLabel for="name" value="Nombre Completo" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" autofocus />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <InputLabel for="email" value="Correo Electrónico" />
                        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div>
                        <InputLabel for="role" value="Rol" />
                        <select id="role" v-model="form.role"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="">Seleccione un rol</option>
                            <option v-for="role in filteredRoles" :value="role.name" :key="role.id">
                                {{ role.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.role" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <InputLabel for="password" value="Contraseña" />
                        <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full"
                            placeholder="Dejar en blanco para mantener la actual" />
                        <InputError :message="form.errors.password" class="mt-2" />
                        <p v-if="editingUser" class="text-xs text-gray-500 mt-1">
                            Solo llena este campo si deseas cambiar la contraseña.
                        </p>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ml-3 bg-green-600 hover:bg-green-700 active:bg-green-800"
                    :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="save">
                    {{ editingUser ? 'Actualizar' : 'Guardar' }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

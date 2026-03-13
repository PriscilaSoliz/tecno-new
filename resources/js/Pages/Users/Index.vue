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
import { useNotifications } from '@/Composables/useNotifications';

const { confirm, success, error } = useNotifications();

const props = defineProps({
    users: Object, 
    roles: Array,
});

// Estados para búsqueda y ordenamiento 
const searchQuery = ref('');
const statusFilter = ref(''); 
const sortField = ref('id');
const sortDirection = ref('desc');

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
    telefono: '',
    password: '',
});

const phoneInvalid = computed(() => {
    return form.telefono && form.telefono.length > 0 && form.telefono.length !== 8;
});

const openModal = (user = null) => {
    editingUser.value = user;
    if (user) {
        form.name = user.name;
        form.email = user.email;
        form.role = user.roles && user.roles.length ? user.roles[0].name : '';
        form.telefono = user.telefono || '';
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
            onSuccess: () => {
                closeModal();
                success('Usuario actualizado correctamente');
            },
            onError: () => {
                error('Error al actualizar el usuario. Verifique los datos.');
            }
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => {
                closeModal();
                success('Usuario registrado correctamente');
            },
            onError: () => {
                error('Error al registrar el usuario. Verifique los datos.');
            }
        });
    }
};

const deleteUser = async (user) => {
    const isConfirmed = await confirm('¿Estás seguro de que deseas eliminar a ' + user.name + '?', 'Eliminar Usuario');
    if (isConfirmed) {
        router.delete(route('users.destroy', user.id), {
            onSuccess: () => {
                success('Usuario eliminado correctamente');
            },
            onError: () => {
                error('No se pudo eliminar el usuario');
            }
        });
    }
};

const normalizeText = (text) => (text || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

const filteredUsers = computed(() => {
    const searchNormalized = normalizeText(searchQuery.value);
    
    return props.users.data.filter(user => {
        const nameNorm = normalizeText(user.name);
        const emailNorm = normalizeText(user.email);
        return nameNorm.includes(searchNormalized) || emailNorm.includes(searchNormalized);
    });
});

const sortedUsers = computed(() => {
    return [...filteredUsers.value].sort((a, b) => {
        let aValue = a[sortField.value];
        let bValue = b[sortField.value];

        if (typeof aValue === 'string') {
            aValue = aValue.toLowerCase();
            bValue = bValue.toLowerCase();
        }

        if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1;
        if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1;
        return 0;
    });
});

const sortBy = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
};
</script>

<template>
    <AppLayout title="Gestión de Usuarios">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestión de Usuarios
                </h2>
                <button 
                    @click="openModal(null)"
                    class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center justify-center transition-colors shadow-sm text-sm font-semibold uppercase tracking-widest active:scale-95"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Nuevo Usuario
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    
                    <!-- Barra de Búsqueda y Filtros (Estilo Proveedores) -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        v-model="searchQuery"
                                        placeholder="Buscar usuarios por nombre o correo..." 
                                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                    >
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vista de Tabla (Escritorio) -->
                    <div class="hidden lg:block overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th 
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors whitespace-nowrap"
                                        @click="sortBy('id')"
                                    >
                                        <div class="flex items-center">
                                            ID
                                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                                        </div>
                                    </th>
                                    <th 
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors whitespace-nowrap"
                                        @click="sortBy('name')"
                                    >
                                        <div class="flex items-center">
                                            NOMBRE
                                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                                        </div>
                                    </th>
                                    <th 
                                        class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors whitespace-nowrap"
                                        @click="sortBy('email')"
                                    >
                                        <div class="flex items-center">
                                            CORREO
                                            <i class="fas fa-sort ml-1 text-gray-400"></i>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider whitespace-nowrap text-center">
                                        TELÉFONO
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider whitespace-nowrap text-center">
                                        ROL
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider whitespace-nowrap">
                                        ACCIONES
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in sortedUsers" :key="user.id"
                                    class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ user.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-indigo-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ user.name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">
                                        {{ user.telefono || '...' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span v-for="role in user.roles" :key="role.id"
                                            class="px-2 py-1 inline-flex text-[10px] leading-5 font-black uppercase rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-1">
                                            {{ role.name }}
                                        </span>
                                        <span v-if="!user.roles || user.roles.length === 0"
                                            class="text-xs text-gray-400 italic">
                                            Sin rol
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex justify-center space-x-3">
                                            <button @click="openModal(user)"
                                                class="text-blue-600 hover:text-blue-900 flex items-center transition-all active:scale-95"
                                                title="Editar">
                                                <i class="fas fa-edit mr-1"></i> Editar
                                            </button>
                                            <button @click="deleteUser(user)"
                                                class="text-red-600 hover:text-red-900 flex items-center transition-all active:scale-95"
                                                title="Eliminar">
                                                <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="sortedUsers.length === 0">
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                        <p>No se encontraron usuarios.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Vista de Tarjetas (Móvil) -->
                    <div class="lg:hidden p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="user in sortedUsers" :key="'card-' + user.id"
                            class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-xl transition-all active:scale-[0.98]">
                            
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 text-2xl shadow-inner">
                                    <i class="fas fa-user-astronaut"></i>
                                </div>
                                <span class="text-[10px] font-black text-gray-400 bg-gray-50 dark:bg-gray-700 px-3 py-1 rounded-full uppercase tracking-widest border border-gray-100 dark:border-gray-600">ID #{{ user.id }}</span>
                            </div>

                            <div class="space-y-4 mb-6">
                                <div>
                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">USUARIO</p>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight truncate">{{ user.name }}</h3>
                                    <p class="text-xs text-indigo-600 dark:text-indigo-400 font-bold flex items-center gap-1.5 mt-1">
                                        <i class="fas fa-envelope text-[10px]"></i>
                                        {{ user.email }}
                                    </p>
                                </div>

                                <div class="flex gap-3">
                                    <div class="flex-1 bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-3 border border-gray-100 dark:border-gray-600">
                                        <p class="text-[8px] font-black text-gray-400 uppercase mb-1">TELÉFONO</p>
                                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                            <i class="fas fa-phone-alt text-[10px] text-gray-400"></i>
                                            {{ user.telefono || '...' }}
                                        </p>
                                    </div>
                                    <div class="flex-1 bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-3 border border-gray-100 dark:border-gray-600">
                                        <p class="text-[8px] font-black text-gray-400 uppercase mb-1">ROL</p>
                                        <div class="flex flex-wrap gap-1">
                                            <span v-for="role in user.roles" :key="role.id"
                                                class="px-2 py-0.5 bg-indigo-600 text-white text-[8px] font-black rounded-lg uppercase tracking-tighter">
                                                {{ role.name }}
                                            </span>
                                            <span v-if="!user.roles || user.roles.length === 0" class="text-[9px] text-gray-400 italic">Sin rol</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-dashed border-gray-100 dark:border-gray-700">
                                <button @click="openModal(user)"
                                    class="flex items-center justify-center gap-2 py-3 bg-blue-50 text-blue-600 rounded-2xl font-black text-[10px] uppercase tracking-wider hover:bg-blue-600 hover:text-white transition-all active:scale-95 shadow-sm">
                                    <i class="fas fa-user-edit"></i>
                                    Editar
                                </button>
                                <button @click="deleteUser(user)"
                                    class="flex items-center justify-center gap-2 py-3 bg-rose-50 text-rose-600 rounded-2xl font-black text-[10px] uppercase tracking-wider hover:bg-rose-600 hover:text-white transition-all active:scale-95 shadow-sm">
                                    <i class="fas fa-user-minus"></i>
                                    Eliminar
                                </button>
                            </div>
                        </div>

                        <div v-if="sortedUsers.length === 0" class="col-span-full py-16 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-4 shadow-inner">
                                <i class="fas fa-users-slash text-4xl text-gray-200"></i>
                            </div>
                            <h4 class="text-lg font-black text-gray-400 uppercase tracking-tighter">No hay usuarios registrados</h4>
                            <p class="text-xs text-gray-300">Tus criterios de búsqueda no coinciden.</p>
                        </div>
                    </div>

                    <!-- Pagination (Estilo Proveedores) -->
                    <div class="px-6 py-4 border-t border-gray-200" v-if="users.links && users.links.length > 3">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Mostrando {{ sortedUsers.length }} usuarios en esta página
                            </div>
                            <div class="flex flex-wrap -mb-1">
                                <template v-for="(link, k) in users.links" :key="k">
                                    <div v-if="link.url === null"
                                        class="mr-1 mb-1 px-3 py-2 text-sm leading-4 text-gray-400 border rounded"
                                        v-html="link.label" />
                                    <a v-else
                                        class="mr-1 mb-1 px-3 py-2 text-sm leading-4 border rounded hover:bg-gray-50 transition-colors focus:border-indigo-500 focus:text-indigo-500"
                                        :class="{ 'bg-indigo-600 text-white hover:bg-indigo-700': link.active }" :href="link.url"
                                        v-html="link.label" />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal (Manteniendo funcionalidad original pero con estilos limpios) -->
        <DialogModal :show="showingModal" @close="closeModal">
            <template #title>
                <div class="text-lg font-bold text-gray-800">
                    {{ editingUser ? 'Editar Usuario' : 'Nuevo Usuario' }}
                </div>
            </template>

            <template #content>
                <div class="grid grid-cols-1 gap-5 py-2">
                    <!-- Name -->
                    <div>
                        <InputLabel for="name" value="Nombre Completo" class="text-gray-700 font-semibold mb-1" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full rounded-lg" placeholder="Ej. Juan Pérez" autofocus />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <InputLabel for="email" value="Correo Electrónico" class="text-gray-700 font-semibold mb-1" />
                        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full rounded-lg" placeholder="usuario@ejemplo.com" />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div>
                        <InputLabel for="role" value="Rol" class="text-gray-700 font-semibold mb-1" />
                        <select id="role" v-model="form.role"
                            class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <option value="">Seleccione un rol</option>
                            <option v-for="role in filteredRoles" :value="role.name" :key="role.id">
                                {{ role.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.role" class="mt-2" />
                    </div>

                    <!-- Telefono -->
                    <div>
                        <InputLabel for="telefono" value="Teléfono" class="text-gray-700 font-semibold mb-1" />
                        <TextInput id="telefono" v-model="form.telefono" type="text" class="mt-1 block w-full rounded-lg"
                            placeholder="Ej. 70000000" maxlength="8"
                            @keypress="(e) => { if (!/[0-9]/.test(e.key)) e.preventDefault(); }"
                            @input="form.telefono = form.telefono.replace(/\D/g, '')" />
                        <InputError v-if="phoneInvalid" class="mt-2" message="El teléfono debe tener exactamente 8 dígitos" />
                        <InputError v-else :message="form.errors.telefono" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <InputLabel for="password" value="Contraseña" class="text-gray-700 font-semibold mb-1" />
                        <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full rounded-lg"
                            :placeholder="editingUser ? 'Dejar en blanco para mantener la actual' : 'Mínimo 8 caracteres'" />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </div>
            </template>

            <template #footer>
                <div class="flex space-x-3">
                    <SecondaryButton @click="closeModal" class="rounded-lg">
                        Cancelar
                    </SecondaryButton>

                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-bold transition-all shadow-md active:scale-95 disabled:opacity-50"
                        :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="save">
                        {{ editingUser ? 'Actualizar Usuario' : 'Registrar Usuario' }}
                    </button>
                </div>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<style scoped>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

tr {
    animation: fade-in 0.3s ease-out;
}
</style>


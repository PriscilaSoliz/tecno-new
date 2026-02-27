<script setup>
import { computed, onMounted } from 'vue'; // Se agregó onMounted
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    menu: Object,
    parents: Array,
    availableRoles: Array,
});

// Normalizar los roles que llegan del backend (array o JSON string)
const normalizeRoles = (value) => {
    if (Array.isArray(value)) return value;
    if (typeof value === 'string' && value.trim()) {
        try { 
            const parsed = JSON.parse(value);
            // Asegurar que el resultado sea un array
            return Array.isArray(parsed) ? parsed : []; 
        } catch { 
            return [value]; 
        }
    }
    return [];
};

// Inicializar el formulario con los datos del menú
const form = useForm({
    title: props.menu?.title || '',
    route: props.menu?.route || '',
    icon: props.menu?.icon || '',
    parent_id: props.menu?.parent_id || null,
    order: props.menu?.order || 1,
    is_active: props.menu?.is_active ?? true,
    roles: normalizeRoles(props.menu?.roles),
});

// Asegurar que siempre haya una lista de roles disponible
const availableRolesList = computed(() => Array.isArray(props.availableRoles) ? props.availableRoles : []);

// Resumen de roles seleccionados
const selectedRolesSummary = computed(() =>
    Array.isArray(form.roles) && form.roles.length ? form.roles.join(', ') : 'Ninguno (visible para todos los usuarios autenticados)'
);

const rolePalette = ['propietario','encargadoalmacen','produccion','cliente'];
const showRoleCheck = (role) => Array.isArray(form.roles) && form.roles.includes(role);
const toggleRole = (role) => {
    if (!Array.isArray(form.roles)) form.roles = [];
    
    form.roles = showRoleCheck(role)
        ? form.roles.filter(r => r !== role)
        : [...form.roles, role];
};

const submit = () => {
    form.put(route('menu.update', props.menu.id));
};

const volver = () => router.get(route('menu.index'));

// Debug: mostrar datos al cargar
onMounted(() => {
    console.log('Menu data:', props.menu);
    console.log('Available roles:', availableRolesList.value);
});
</script>

<template>
    <AppLayout title="Editar Elemento de Menú">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800">Editar Elemento de Menú</h2>
                <button @click="volver" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-1"></i> Volver
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Layout principal -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Card principal -->
                    <div class="lg:col-span-2 space-y-6">
                        <section class="bg-white dark:bg-gray-800 rounded-2xl shadow px-6 py-5 border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <i class="fas fa-layer-group text-amber-500"></i> Información general
                            </h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <!-- título -->
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-300">Título *</label>
                                    <input v-model="form.title" type="text" required class="mt-1 w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700" placeholder="Ej. Gestor de pedidos" />
                                    <p v-if="form.errors.title" class="text-xs text-rose-500 mt-1">{{ form.errors.title }}</p>
                                </div>
                                <!-- ruta -->
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-300">Nombre de ruta</label>
                                    <input v-model="form.route" type="text" class="mt-1 w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700" placeholder="dashboard, pedidos.index..." />
                                    <small class="text-xs text-gray-500">Debe coincidir con <code>name()</code> en web.php.</small>
                                </div>
                                <!-- icono -->
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-300">Icono (FontAwesome)</label>
                                    <div class="flex items-center gap-2 mt-1">
                                        <input v-model="form.icon" type="text" class="flex-1 rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700" placeholder="fas fa-bread-slice" />
                                        <span class="w-12 h-12 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-600">
                                            <i :class="form.icon || 'fas fa-icons'"></i>
                                        </span>
                                    </div>
                                </div>
                                <!-- orden -->
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-300">Orden *</label>
                                    <input v-model.number="form.order" type="number" min="1" required class="mt-1 w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700" />
                                    <small class="text-xs text-gray-500">Posición en el menú (1 = primero).</small>
                                </div>
                                <!-- padre -->
                                <div>
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-300">Menú padre</label>
                                    <select v-model="form.parent_id" class="mt-1 w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700">
                                        <option :value="null">Sin padre (nivel raíz)</option>
                                        <option v-for="parent in parents" :key="parent.id" :value="parent.id">
                                            {{ parent.title }}
                                        </option>
                                    </select>
                                </div>
                                <!-- estado -->
                                <div class="flex flex-col justify-end">
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-300 mb-2">Visibilidad</label>
                                    <button type="button"
                                            @click="form.is_active = !form.is_active"
                                            :class="form.is_active ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-500 border-gray-200'"
                                            class="flex items-center justify-between rounded-xl border px-4 py-2 transition">
                                        <span>{{ form.is_active ? 'Activo' : 'Oculto' }}</span>
                                        <i :class="form.is_active ? 'fas fa-toggle-on text-emerald-500' : 'fas fa-toggle-off text-gray-400'"></i>
                                    </button>
                                </div>
                            </div>
                        </section>

                        <!-- roles -->
                        <section class="bg-white dark:bg-gray-800 rounded-2xl shadow px-6 py-5 border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                    <i class="fas fa-user-shield text-blue-500"></i> Roles con acceso
                                </h3>
                                <span class="text-xs text-gray-500">Selecciona roles para restringir el acceso</span>
                            </div>

                            <div v-if="availableRolesList.length" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <label
                                    v-for="role in availableRolesList"
                                    :key="role"
                                    class="flex items-center gap-3 px-3 py-2 rounded-xl border cursor-pointer transition"
                                    :class="showRoleCheck(role) ? 'border-amber-300 bg-amber-50 text-amber-700' : 'border-gray-200 text-gray-500'">
                                    <input type="checkbox" class="hidden" :checked="showRoleCheck(role)" @change="toggleRole(role)" />
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center bg-white shadow">
                                        <i :class="showRoleCheck(role) ? 'fas fa-check text-amber-500' : 'fas fa-user text-gray-300'"></i>
                                    </span>
                                    <span class="capitalize">{{ role }}</span>
                                </label>
                            </div>
                            <p v-else class="text-sm text-gray-500">No hay roles registrados. Ejecuta los seeders de roles.</p>

                            <div class="mt-4 p-4 rounded-xl bg-blue-50 border border-blue-100 text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-2"></i>
                                <strong>Resumen:</strong> {{ selectedRolesSummary }}
                            </div>

                            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div
                                    v-for="role in rolePalette"
                                    :key="role"
                                    class="rounded-xl border px-3 py-2 flex items-center justify-between text-sm capitalize"
                                    :class="showRoleCheck(role) ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-400'">
                                    {{ role }}
                                    <i :class="showRoleCheck(role) ? 'fas fa-check text-emerald-500' : 'fas fa-minus text-gray-300'"></i>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- sidebar -->
                    <aside class="space-y-4">
                        <div class="bg-gradient-to-br from-amber-500 to-orange-400 text-white rounded-2xl shadow-lg p-5">
                            <h4 class="text-lg font-semibold mb-3 flex items-center gap-2">
                                <i class="fas fa-eye"></i> Vista previa de acceso
                            </h4>
                            <p class="text-sm text-amber-50 mb-4">
                                Los roles seleccionados verán esta pestaña automáticamente en el menú principal.
                            </p>
                            <ul class="space-y-2 text-sm">
                                <li v-for="role in rolePalette" :key="role" class="flex items-center justify-between">
                                    <span class="capitalize">{{ role }}</span>
                                    <i :class="showRoleCheck(role) ? 'fas fa-check-circle text-white' : 'fas fa-circle-xmark text-amber-200'"></i>
                                </li>
                            </ul>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gray-100 dark:border-gray-700 p-5">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-3">Acciones</h4>
                            <div class="space-y-3">
                                <button type="button" @click="volver"
                                        class="w-full px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Cancelar
                                </button>
                                <button type="button" @click="submit"
                                        class="w-full px-4 py-2 rounded-xl bg-amber-600 text-white font-semibold hover:bg-amber-700 disabled:opacity-50"
                                        :disabled="form.processing">
                                    {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
                                </button>
                            </div>
                            <p class="mt-4 text-xs text-gray-400">Los cambios se verán reflejados inmediatamente en la navegación.</p>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

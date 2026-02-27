<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAccessibility } from '@/Composables/useAccessibility';

const { theme, fontSize, fontFamily, isDark, setTheme, setFontSize, setFontFamily, toggleDark, resetToDefault } = useAccessibility();

const themes = [
    { value: 'default', label: 'Predeterminado', description: 'Tema clásico y profesional', icon: '🎨', color: 'from-gray-500 to-blue-600' },
    { value: 'nino', label: 'Modo Niño', description: 'Colores brillantes y alegres', icon: '🎈', color: 'from-pink-500 to-cyan-500' },
    { value: 'joven', label: 'Modo Joven', description: 'Diseño moderno y vibrante', icon: '🚀', color: 'from-purple-600 to-cyan-600' }
];

const fontSizes = [
    { value: 'small', label: 'Pequeño (14px)' },
    { value: 'medium', label: 'Mediano (16px)' },
    { value: 'large', label: 'Grande (18px)' },
    { value: 'xlarge', label: 'Muy Grande (20px)' }
];

const fontFamilies = [
    { value: 'system', label: 'Sistema', preview: 'Aa' },
    { value: 'sans', label: 'Sans Serif', preview: 'Aa' },
    { value: 'serif', label: 'Serif', preview: 'Aa' },
    { value: 'mono', label: 'Monoespaciada', preview: 'Aa' }
];

const showResetConfirm = ref(false);
const showVisitCounterModal = ref(false);
const isResettingVisits = ref(false);
const showSuccessToast = ref(false);
const successToastMessage = ref('');

const page = usePage();

// Mostrar mensaje de éxito cuando Laravel devuelve flash message
const checkFlashMessages = () => {
    if (page.props.flash?.success) {
        successToastMessage.value = page.props.flash.success;
        showSuccessToast.value = true;
        setTimeout(() => {
            showSuccessToast.value = false;
        }, 3000);
    }
};

// Verificar mensajes al cargar
checkFlashMessages();

const resetVisitCounters = async () => {
    isResettingVisits.value = true;
    
    router.post(route('page-visits.reset-all'), {}, {
        preserveState: false,
        onFinish: () => {
            isResettingVisits.value = false;
            showVisitCounterModal.value = false;
        }
    });
};
</script>

<template>
    <AppLayout title="Accesibilidad">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
                ⚙️ Configuración de Accesibilidad
            </h2>
        </template>

        <!-- Toast de Éxito (limpio y minimalista) -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-2 opacity-0"
        >
            <div v-if="showSuccessToast" class="fixed top-4 right-4 z-50">
                <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ successToastMessage }}</span>
                </div>
            </div>
        </Transition>

        <div class="py-12 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Modo Oscuro -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                🌙 Modo Oscuro
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Reduce la fatiga visual en ambientes con poca luz
                            </p>
                        </div>
                        <button
                            @click="toggleDark"
                            :class="[
                                'relative inline-flex h-12 w-24 items-center rounded-full transition-all duration-300',
                                isDark ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'
                            ]"
                        >
                            <span
                                :class="[
                                    'inline-block h-10 w-10 transform rounded-full bg-white shadow-md transition-transform duration-300',
                                    isDark ? 'translate-x-12' : 'translate-x-1'
                                ]"
                            >
                                <svg v-if="isDark" class="h-full w-full p-2 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21.64 15.95a.75.75 0 0 0-.5-.75A8.049 8.049 0 0 0 12 6a8.05 8.05 0 0 0-9.14 9.14.75.75 0 0 0 .75.5.75.75 0 0 0 .75-.75 6.55 6.55 0 0 1 7.64-7.64 6.55 6.55 0 0 1 7.64 7.64.75.75 0 0 0 .75.75h.5a.75.75 0 0 0 .75-.75z" />
                                </svg>
                                <svg v-else class="h-full w-full p-2 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 17a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0-8a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm-1-8h2v3h-2V1zm8.66 1.34l-2.12 2.12-1.41-1.41 2.12-2.12 1.41 1.41zM20 10h3v2h-3v-2zM2 10h3v2H2v-2zm17.66 8.66l2.12 2.12-1.41 1.41-2.12-2.12 1.41-1.41zm-14.32 0l1.41 1.41-2.12 2.12-1.41-1.41 2.12-2.12zM12 21v3h-2v-3h2zM1 12h3v2H1v-2z" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Temas Visuales -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-purple-500">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        🎨 Tema Visual
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Selecciona el estilo visual que prefieras para la interfaz
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button
                            v-for="themeOption in themes"
                            :key="themeOption.value"
                            @click="setTheme(themeOption.value)"
                            :class="[
                                'p-6 rounded-lg border-2 transition-all duration-300 text-left hover:shadow-lg',
                                theme === themeOption.value
                                    ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30 ring-2 ring-purple-500'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-purple-400 dark:hover:border-purple-500'
                            ]"
                        >
                            <div class="text-5xl mb-4">{{ themeOption.icon }}</div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100 mb-1 text-lg">
                                {{ themeOption.label }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                {{ themeOption.description }}
                            </div>
                            <div v-if="theme === themeOption.value" class="flex items-center text-purple-600 dark:text-purple-400 text-sm font-bold">
                                <i class="fas fa-check-circle mr-2"></i> Activo
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Tamaño de Fuente -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        📏 Tamaño de Fuente
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Ajusta el tamaño del texto para mejorar la legibilidad
                    </p>
                    
                    <div class="space-y-3">
                        <button
                            v-for="size in fontSizes"
                            :key="size.value"
                            @click="setFontSize(size.value)"
                            :class="[
                                'w-full p-4 rounded-lg border-2 transition-all text-left flex items-center justify-between hover:shadow-md',
                                fontSize === size.value
                                    ? 'border-green-500 bg-green-50 dark:bg-green-900/30 ring-2 ring-green-500'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-green-400 dark:hover:border-green-500'
                            ]"
                        >
                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                {{ size.label }}
                            </span>
                            <span v-if="fontSize === size.value" class="text-green-600 dark:text-green-400 font-bold text-lg">
                                ✓
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Tipo de Fuente -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-orange-500">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        🔤 Tipo de Fuente
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Elige la familia de fuente que prefieras
                    </p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <button
                            v-for="font in fontFamilies"
                            :key="font.value"
                            @click="setFontFamily(font.value)"
                            :class="[
                                'p-4 rounded-lg border-2 transition-all flex flex-col items-center hover:shadow-md',
                                fontFamily === font.value
                                    ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/30 ring-2 ring-orange-500'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-orange-400 dark:hover:border-orange-500'
                            ]"
                        >
                            <div class="text-4xl mb-2 font-bold text-gray-900 dark:text-gray-100">
                                {{ font.preview }}
                            </div>
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100 text-center">
                                {{ font.label }}
                            </div>
                            <div v-if="fontFamily === font.value" class="mt-2 text-orange-600 dark:text-orange-400 text-xs font-bold">
                                ✓ Activa
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Vista Previa -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-indigo-500">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        👁️ Vista Previa
                    </h3>
                    <div class="p-6 rounded-lg bg-gray-50 dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700">
                        <h4 class="text-2xl font-bold mb-3 text-gray-900 dark:text-gray-100">Título de Ejemplo</h4>
                        <p class="mb-3 text-gray-700 dark:text-gray-300 leading-relaxed">
                            Este es un texto de ejemplo para que puedas ver cómo se verán tus configuraciones.
                            Los cambios se aplicarán en toda la interfaz automáticamente.
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Texto secundario en tamaño reducido para mayor flexibilidad.
                        </p>
                    </div>
                </div>

                <!-- Contador de Visitas -->
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-cyan-500">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        📊 Contador de Visitas
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Reinicia el contador de visitas de todas las páginas del sistema
                    </p>

                    <button
                        @click="showVisitCounterModal = true"
                        class="px-6 py-3 bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-700 dark:hover:bg-cyan-600 text-white font-medium rounded-lg transition-colors shadow-md"
                    >
                        <i class="fas fa-redo mr-2"></i>
                        Reiniciar Contadores
                    </button>
                </div>

                <!-- Botón de Restablecer -->
                <div class="flex justify-end">
                    <button
                        @click="showResetConfirm = true"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white font-medium rounded-lg transition-colors shadow-md"
                    >
                        🔄 Restablecer a Valores Predeterminados
                    </button>
                </div>

                <!-- Modal de Confirmación para Restablecer Configuración -->
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showResetConfirm" class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-50" @click="showResetConfirm = false">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md mx-4 shadow-xl border border-gray-200 dark:border-gray-700" @click.stop>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">
                                ¿Restablecer configuración?
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Esto restablecerá todas las configuraciones de accesibilidad a sus valores predeterminados.
                            </p>
                            <div class="flex gap-3 justify-end">
                                <button
                                    @click="showResetConfirm = false"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                                >
                                    Cancelar
                                </button>
                                <button
                                    @click="resetToDefault(); showResetConfirm = false"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                                >
                                    Restablecer
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Modal para confirmar reinicio de contadores -->
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <div v-if="showVisitCounterModal" class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-50" @click="showVisitCounterModal = false">
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-md mx-4 shadow-xl border border-gray-200 dark:border-gray-700" @click.stop>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">
                                ¿Reiniciar contadores?
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">
                                Esto reiniciará los contadores de visitas de TODAS las páginas.
                            </p>
                            <div class="flex gap-3 justify-end">
                                <button
                                    @click="showVisitCounterModal = false"
                                    :disabled="isResettingVisits"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors disabled:opacity-50"
                                >
                                    Cancelar
                                </button>
                                <button
                                    @click="resetVisitCounters"
                                    :disabled="isResettingVisits"
                                    class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg transition-colors disabled:opacity-50 flex items-center"
                                >
                                    <i v-if="isResettingVisits" class="fas fa-spinner fa-spin mr-2"></i>
                                    {{ isResettingVisits ? 'Procesando...' : 'Reiniciar' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useAccessibility } from '@/Composables/useAccessibility';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const images = [
    'https://images.unsplash.com/photo-1509440159596-0249088772ff?q=80&w=1972&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1556910103-1c02745a30bf?q=80&w=2070&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?q=80&w=1887&auto=format&fit=crop'
];

const currentImageIndex = ref(0);
const isPaused = ref(false);
let intervalId = null;
const INTERVAL_MS = 4500;

onMounted(() => {
    intervalId = setInterval(() => {
        if (!isPaused.value) {
            currentImageIndex.value = (currentImageIndex.value + 1) % images.length;
        }
    }, INTERVAL_MS);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});

const { theme, fontSize, fontFamily, isDark } = useAccessibility();

function applyAccessibility() {
    const root = document.documentElement;
    if (!root) return;

    root.setAttribute('data-theme', theme.value || 'default');

    if (isDark.value) root.classList.add('dark');
    else root.classList.remove('dark');

    const sizeMap = { small: '14px', medium: '16px', large: '18px', xlarge: '20px' };
    root.style.setProperty('--font-size-base', sizeMap[fontSize.value] ?? '16px');

    const familyMap = {
        system: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        sans: 'Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial',
        serif: 'Georgia, "Times New Roman", Times, serif',
        mono: 'ui-monospace, SFMono-Regular, Menlo, Monaco, "Roboto Mono", monospace'
    };
    root.style.setProperty('--font-family-base', familyMap[fontFamily.value] ?? familyMap.system);
}

onMounted(() => {
    applyAccessibility();
    watch([theme, fontSize, fontFamily, isDark], applyAccessibility, { immediate: true });
});
</script>

<template>
    <Head title="Iniciar Sesión" />
    <div class="page-container">
        <div class="min-h-screen" :style="{ backgroundColor: 'var(--bg-light)', color: 'var(--text-primary)' }">
            <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">
                <!-- Columna del Formulario -->
                <div class="flex flex-col justify-center items-center px-6 py-12 lg:px-8">
                    <div class="mx-auto w-full max-w-sm">
                        <Link href="/" class="inline-block">
                            <h2 class="text-3xl font-bold tracking-tight" :style="{ color: 'var(--text-primary)' }">
                                Pan de Casa
                            </h2>
                        </Link>
                        <h3 class="mt-4 text-xl font-semibold text-center" style="color:#4B4B45">
                            Bienvenido de vuelta
                        </h3>

                        <div v-if="status" class="mb-4 mt-6 font-medium text-sm text-green-600">
                            {{ status }}
                        </div>

                        <form @submit.prevent="submit" class="mt-10">
                            <div>
                                <InputLabel for="email" value="Correo Electrónico" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="password" value="Contraseña" />
                                <TextInput
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    required
                                    autocomplete="current-password"
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div class="block mt-4">
                                <label class="flex items-center">
                                    <Checkbox v-model:checked="form.remember" name="remember" />
                                    <span class="ms-2 text-sm" style="color:#4B4B45">Recuérdame</span>
                                </label>
                            </div>

                            <div class="flex items-center justify-between mt-6">
                                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" style="color:#4B4B45">
                                    ¿Olvidaste tu contraseña?
                                </Link>

                                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" style="background-color: #F6C85F; color:#6B4226;">
                                    Iniciar Sesión
                                </PrimaryButton>
                            </div>
                             <div class="text-center mt-6">
                                <Link :href="route('register')" class="text-sm underline" style="color:#4B4B45">
                                    ¿No tienes una cuenta? Regístrate
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Columna del Carrusel -->
                <div
                    class="relative h-64 w-full lg:h-full overflow-hidden hidden lg:block"
                    @mouseenter="isPaused = true"
                    @mouseleave="isPaused = false"
                    aria-live="polite"
                >
                    <div 
                        v-for="(img, index) in images" 
                        :key="img"
                        role="img"
                        :aria-hidden="index === currentImageIndex ? 'false' : 'true'"
                        class="absolute inset-0 bg-cover bg-center img-slide"
                        :class="{ 'active': index === currentImageIndex }"
                        :style="{ backgroundImage: `url('${img}')` }"
                    ></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-50 via-emerald-50/60 to-transparent lg:bg-gradient-to-l pointer-events-none"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.img-slide {
    opacity: 0;
    transform: scale(1.1) translateY(0);
    transition: opacity 1.1s cubic-bezier(.2,.9,.3,1), transform 1.3s cubic-bezier(.2,.9,.3,1);
    will-change: opacity, transform;
}

.img-slide.active {
    opacity: 1;
    transform: scale(1) translateY(0);
    z-index: 10;
}

@media (prefers-reduced-motion: reduce) {
    .img-slide, .img-slide.active {
        transition: none;
    }
}
</style>

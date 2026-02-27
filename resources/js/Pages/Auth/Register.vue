<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useAccessibility } from '@/Composables/useAccessibility';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

// Carrusel de imágenes (panaderos)
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
            // usar mismo sentido que Welcome (contrario) para consistencia visual
            currentImageIndex.value = (currentImageIndex.value - 1 + images.length) % images.length;
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
    <Head title="Registro - Pan de Casa">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </Head>

    <div class="min-h-screen" :style="{ backgroundColor: 'var(--bg-light)', color: 'var(--text-primary)' }">
        <header class="absolute inset-x-0 top-0 z-50">
            <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
                <div class="flex lg:flex-1">
                    <a href="/" class="-m-1.5 p-1.5">
                        <span class="text-2xl font-bold tracking-tight">Pan de Casa</span>
                    </a>
                </div>
            </nav>
        </header>

        <div class="relative isolate min-h-screen">
            <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">
                <!-- formulario (columna izquierda) -->
                <div class="flex flex-col justify-center items-center px-6 py-12 lg:px-8">
                    <div class="mx-auto w-full max-w-md">
                        <h2 class="text-3xl font-bold tracking-tight text-center" :style="{ color: 'var(--text-primary)' }">Crear cuenta</h2>
                        <p class="mt-2 text-center text-sm" :style="{ color: 'var(--text-secondary)' }">
                            Únete a Pan de Casa — recetas artesanales y pan fresco cada día.
                        </p>

                        <form @submit.prevent="submit" class="mt-8">
                            <div>
                                <InputLabel for="name" value="Nombre" style="color:#6B4226" />
                                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="email" value="Correo Electrónico" style="color:#6B4226" />
                                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autocomplete="username" />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mt-4 lg:flex lg:gap-4">
                                <div class="lg:flex-1">
                                    <InputLabel for="password" value="Contraseña" style="color:#6B4226" />
                                    <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                                    <InputError class="mt-2" :message="form.errors.password" />
                                </div>

                                <div class="mt-4 lg:mt-0 lg:flex-1">
                                    <InputLabel for="password_confirmation" value="Confirmar Contraseña" style="color:#6B4226" />
                                    <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                                </div>
                            </div>

                            <div v-if="$page.props.jetstream?.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                                <label class="flex items-start gap-3">
                                    <Checkbox id="terms" v-model:checked="form.terms" name="terms" />
                                    <div class="text-sm" style="color:#4B4B45">
                                        Acepto los
                                        <a target="_blank" :href="route('terms.show')" class="underline">Términos de Servicio</a>
                                        y la
                                        <a target="_blank" :href="route('policy.show')" class="underline">Política de Privacidad</a>
                                    </div>
                                </label>
                                <InputError class="mt-2" :message="form.errors.terms" />
                            </div>

                            <div class="mt-6 flex items-center justify-between">
                                <Link :href="route('login')" class="text-sm underline" style="color:#4B4B45">¿Ya tienes cuenta? Inicia sesión</Link>

                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" style="background-color:#F6C85F;color:#6B4226;">
                                    Registrarse
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- carrusel (columna derecha) -->
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
    transform: scale(1.06) translateY(6%);
    transition: opacity 1.5s cubic-bezier(.4,0,.2,1), transform 6s cubic-bezier(.4,0,.2,1);
    will-change: opacity, transform;
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
}

.img-slide.active {
    opacity: 1;
    transform: scale(1) translateY(0);
    z-index: 1;
}

.img-slide::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.02), rgba(0,0,0,0.06));
    pointer-events: none;
}

@media (prefers-reduced-motion: reduce) {
    .img-slide, .img-slide.active {
        transition: none;
    }
}
</style>

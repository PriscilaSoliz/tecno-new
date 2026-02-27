<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { useAccessibility } from '@/Composables/useAccessibility';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const images = [
    'https://images.unsplash.com/photo-1509440159596-0249088772ff?q=80&w=1972&auto=format&fit=crop', // Panadero amasando
    'https://images.unsplash.com/photo-1556910103-1c02745a30bf?q=80&w=2070&auto=format&fit=crop', // Panadero en el horno
    'https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?q=80&w=1887&auto=format&fit=crop'  // Panadero preparando masa
];

const currentImageIndex = ref(0);
const isPaused = ref(false);
let intervalId = null;
const INTERVAL_MS = 4500; // tiempo entre imágenes

onMounted(() => {
    intervalId = setInterval(() => {
        if (!isPaused.value) {
            // sentido inverso: decrementa el índice
            currentImageIndex.value = (currentImageIndex.value - 1 + images.length) % images.length;
        }
    }, INTERVAL_MS);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});

// Accesibilidad
const { theme, fontSize, fontFamily, isDark } = useAccessibility();

function applyAccessibility() {
    const root = document.documentElement;
    if (!root) return;

    // theme attribute used por CSS : [data-theme="nino"] etc.
    root.setAttribute('data-theme', theme.value || 'default');

    // dark mode class
    if (isDark.value) root.classList.add('dark');
    else root.classList.remove('dark');

    // font size variable
    const sizeMap = { small: '14px', medium: '16px', large: '18px', xlarge: '20px' };
    root.style.setProperty('--font-size-base', sizeMap[fontSize.value] ?? '16px');

    // font family variable
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
    <Head title="Pan de Casa - Bienvenido">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </Head>

    <div class="min-h-screen" :style="{ backgroundColor: 'var(--bg-light)', color: 'var(--text-primary)' }">
        <header class="absolute inset-x-0 top-0 z-50">
            <nav class="flex items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="#" class="-m-1.5 p-1.5">
                <span class="text-2xl font-bold tracking-tight">Pan de Casa</span>
                </a>
            </div>
            <div v-if="canLogin" class="flex flex-1 justify-end gap-4">
                <Link
                :href="route('login')"
                class="rounded-md px-4 py-2 text-sm font-semibold transition"
                style="background-color: #F6C85F; color:#6B4226;"
                >
                Iniciar Sesión
                </Link>
                <Link
                v-if="canRegister"
                :href="route('register')"
                class="rounded-md px-4 py-2 text-sm font-semibold border-2 transition hover:bg-white/20"
                style="background-color: #6B4226; color: white; border-color:#6B4226;"
                >
                Registrarse
                </Link>
            </div>
            </nav>
        </header>

        <div class="relative isolate min-h-screen">
            <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">
                <div class="relative px-6 pb-20 pt-36 lg:px-8 lg:py-56">
                    <div class="mx-auto max-w-2xl">
                        <div class="text-center lg:text-left">
                            <h1 class="text-4xl font-bold tracking-tight sm:text-6xl" :style="{ color: 'var(--text-primary)' }">
                                El Sabor de la Tradición, Horneado con Amor
                            </h1>
                            <p class="mt-6 text-lg leading-8" :style="{ color: 'var(--text-secondary)' }">
                                En Pan de Casa, cada pieza es una obra de arte. Usamos ingredientes frescos y recetas de antaño para llevar a tu mesa pan y postres con un sabor inigualable.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- carrusel: pausa al hover y animación mejorada -->
                <div
                    class="relative h-64 w-full lg:h-full overflow-hidden"
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

                    <!-- overlay sutil para lectura -->
                    <div class="absolute inset-0 bg-gradient-to-t from-emerald-50 via-emerald-50/60 to-transparent lg:bg-gradient-to-r pointer-events-none"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* transiciones y efecto parallax/zoom */
.img-slide {
    opacity: 0;
    transform: scale(1) translateY(6%);
    transition: opacity 1.1s cubic-bezier(.2,.9,.3,1), transform 1.3s cubic-bezier(.2,.9,.3,1), filter 1.3s;
    will-change: opacity, transform;
    filter: saturate(0.95) contrast(1);
    background-position: center;
    background-size: cover;
}

/* imagen activa: más visible, ligero zoom y alineación vertical */
.img-slide.active {
    opacity: 1;
    transform: scale(1.06) translateY(0);
    filter: saturate(1.05) contrast(1.02);
    z-index: 10;
}

/* pequeña animación flotante que suaviza la transición
   cuando el carrusel cambia en sentido inverso el efecto se aprecia */
.img-slide::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.02), rgba(0,0,0,0.06));
    pointer-events: none;
}

/* accesibilidad: reduce motion */
@media (prefers-reduced-motion: reduce) {
    .img-slide, .img-slide.active {
        transition: none;
    }
}
</style>

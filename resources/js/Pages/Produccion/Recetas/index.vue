<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import ListaRecetas from './Components/ListaRecetas.vue';
import { usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    recetas: { type: Array, default: () => [] },
    productos: { type: Array, default: () => [] },
    ingredientes: { type: Array, default: () => [] }
});

const page = usePage();
const flashMessage = computed(() => page.props.flash?.success);

const listaRef = ref(null);
const openCreate = () => {
    // try to open modal in child; fallback to navigate to create page
    if (listaRef.value && typeof listaRef.value.openCreateModal === 'function') {
        listaRef.value.openCreateModal();
    } else {
        window.location.href = route('recetas.create');
    }
};
</script>

<template>
    <AppLayout title="Recetas">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de Recetas</h2>

                <!-- call child method to show modal -->
                <button @click="openCreate"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Nueva Receta
                </button>
            </div>
        </template>

        <!-- Mensaje de Éxito -->
        <div v-if="flashMessage" class="fixed top-4 right-4 z-50">
            <div class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2 animate-fade-in">
                <i class="fas fa-check-circle"></i>
                <span>{{ flashMessage }}</span>
            </div>
        </div>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <ListaRecetas ref="listaRef" :recetas="recetas" :productos="productos" :ingredientes="ingredientes" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>

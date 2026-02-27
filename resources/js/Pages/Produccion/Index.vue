<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Tarjetas from './Components/Tarjetas.vue';
</script>

<template>
    <AppLayout title="Producción">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                Gestión de Producción
            </h2>
        </template>

        <div class="py-12" :style="{ backgroundColor: 'var(--bg-light)' }">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-xl sm:rounded-lg p-6" :style="{ backgroundColor: 'var(--bg-light)', color: 'var(--text-primary)' }">
                    <!-- Tarjetas de Producción -->
                    <Tarjetas />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

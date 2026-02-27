<template>
    <AppLayout title="Inventario">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                Gestión de Inventario
            </h2>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- Tarjetas de Gestión de Inventario -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Tarjeta Proveedores -->
                        <a :href="route('proveedores.index')"
                            class="card-hover block bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-2xl shadow-md overflow-hidden border-2 border-purple-300 dark:border-purple-600 hover:shadow-xl transition-all duration-300">
                            <div class="p-6">
                                <div class="flex flex-col items-center text-center">
                                    <div class="icon-container mb-5 p-5 rounded-2xl bg-purple-600 dark:bg-purple-500">
                                        <i class="fas fa-truck-loading text-white text-4xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-purple-900 dark:text-purple-100 mb-2">Proveedores</h3>
                                    <p class="text-purple-700 dark:text-purple-300 mb-4">Gestionar proveedores y órdenes de compra de ingredientes</p>
                                    <div class="flex items-center text-purple-800 dark:text-purple-200 font-bold">
                                        <span>Administrar</span>
                                        <i class="ml-2 fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Tarjeta Almacén -->
                        <a :href="route('almacen.index')"
                            class="card-hover block bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-2xl shadow-md overflow-hidden border-2 border-amber-400 dark:border-amber-600 hover:shadow-xl transition-all duration-300">
                            <div class="p-6">
                                <div class="flex flex-col items-center text-center">
                                    <div class="icon-container mb-5 p-5 rounded-2xl bg-amber-600 dark:bg-amber-500">
                                        <i class="fas fa-warehouse text-white text-4xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-amber-900 dark:text-amber-100 mb-2">Almacén</h3>
                                    <p class="text-amber-700 dark:text-amber-300 mb-4">Control de ingredientes, stock y caducidades</p>
                                    <div class="flex items-center text-amber-800 dark:text-amber-200 font-bold">
                                        <span>Gestionar</span>
                                        <i class="ml-2 fas fa-arrow-right"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<style scoped>
.card-hover {
    transition: all 0.3s ease;
    transform: translateY(0);
}

.card-hover:hover {
    transform: translateY(-5px);
}

.icon-container {
    transition: all 0.3s ease;
}

.card-hover:hover .icon-container {
    transform: scale(1.1) rotate(5deg);
}
</style>
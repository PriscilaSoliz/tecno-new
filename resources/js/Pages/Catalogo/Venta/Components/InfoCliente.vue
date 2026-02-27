<script setup>
defineProps({
    cliente: { type: Object, default: () => ({}) }
});
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-user-circle text-blue-600"></i> Información del Cliente
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                <span class="block text-gray-500 text-xs uppercase mb-1 font-bold tracking-wider">Nombre</span>
                <span class="font-semibold text-gray-800 text-lg">{{ cliente?.name || 'Invitado' }}</span>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                <span class="block text-gray-500 text-xs uppercase mb-1 font-bold tracking-wider">Email</span>
                <span class="font-semibold text-gray-800 text-lg">{{ cliente?.email || '-' }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    productos: { type: Array, default: () => [] },
    cliente: { type: Object, default: () => ({}) },
    total: { type: Number, default: 0 }
});
</script>

<template>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded shadow-sm mb-6">
        <h4 class="font-bold text-yellow-800 mb-2">🔍 Debug: Datos Recibidos</h4>
        <div class="text-sm text-yellow-700 space-y-1">
            <p><strong>Productos:</strong> {{ productos?.length || 0 }} items</p>
            <p><strong>Total:</strong> {{ total }}</p>
            <p><strong>Cliente:</strong> {{ cliente?.name || 'No definido' }}</p>
            <details class="mt-2">
                <summary class="cursor-pointer text-yellow-600 hover:underline">Ver datos completos</summary>
                <pre class="mt-2 bg-yellow-100 p-2 rounded text-xs overflow-auto max-h-40">{{ JSON.stringify({ productos, cliente, total }, null, 2) }}</pre>
            </details>
        </div>
    </div>
</template>

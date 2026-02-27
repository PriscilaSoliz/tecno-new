<script setup>
import { computed } from 'vue';

const props = defineProps({
    total: { type: Number, default: 0 },
    modalidadPago: { type: String, default: 'contado' },
    numeroCuotas: { type: Number, default: 3 },
    processing: { type: Boolean, default: false }
});

const emit = defineEmits(['confirmar']);

const montoPorCuota = computed(() => {
    if (props.modalidadPago !== 'cuotas' || !props.total) return 0;
    return (props.total / props.numeroCuotas).toFixed(2);
});
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-6">
        <div class="text-center mb-6">
            <p class="text-sm text-gray-600 mb-1 uppercase tracking-wide font-semibold">Total a Pagar</p>
            <p class="text-4xl font-extrabold text-amber-600">${{ total.toFixed(2) }}</p>
            <p class="text-4xl font-extrabold text-amber-600">Bs {{ total.toFixed(2) }}</p>
            <p v-if="modalidadPago === 'cuotas'" class="text-sm text-blue-600 font-medium mt-2 bg-blue-50 inline-block px-3 py-1 rounded-full">
                {{ numeroCuotas }} cuotas de ${{ montoPorCuota }}
            </p>
            <p v-if="modalidadPago === 'cuotas'" class="text-sm text-blue-600 font-medium mt-2 bg-blue-50 inline-block px-3 py-1 rounded-full">
                {{ numeroCuotas }} cuotas de Bs {{ montoPorCuota }}
            </p>
        </div>
        
        <button @click="emit('confirmar')" 
                class="w-full bg-gradient-to-r from-emerald-600 to-green-600 text-white py-4 rounded-xl font-bold text-lg hover:from-emerald-700 hover:to-green-700 transition-all shadow-lg shadow-emerald-200 flex items-center justify-center gap-2 transform active:scale-[0.98] hover:scale-[1.01]"
                :disabled="processing">
            <i class="fas fa-lock"></i>
            <span v-if="processing">Procesando...</span>
            <span v-else>Confirmar Pedido</span>
        </button>
        
        <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-400">
            <i class="fas fa-shield-alt"></i>
            <span>Pago 100% seguro y encriptado</span>
        </div>
    </div>
</template>

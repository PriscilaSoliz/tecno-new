<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    show: Boolean,
    total: Number,
    numeroCuotas: Number,
});

const emit = defineEmits(['close', 'confirm']);

const items = ref([]);

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const [year, month, day] = dateStr.split('-');
    return `${day}-${month}-${year}`;
};

// Generar cuotas iniciales
const generarCuotas = () => {
    if (!props.total || !props.numeroCuotas) return;

    const montoBase = Math.floor((props.total / props.numeroCuotas) * 100) / 100;
    const resto = parseFloat((props.total - (montoBase * props.numeroCuotas)).toFixed(2));

    const newItems = [];
    const hoy = new Date();

    for (let i = 0; i < props.numeroCuotas; i++) {
        const fecha = new Date(hoy);
        // Cuota 1: Hoy. Cuota 2..N: +7 días por defecto
        if (i === 0) {
            // fecha hoy
        } else {
            fecha.setDate(fecha.getDate() + (i * 7)); // Default 7 días de intervalo
        }

        let monto = montoBase;
        // Ajustar centavos en la última cuota
        if (i === props.numeroCuotas - 1) {
            monto = parseFloat((monto + resto).toFixed(2));
        }

        newItems.push({
            numero: i + 1,
            fecha: fecha.toISOString().split('T')[0],
            monto: monto,
            estado: i === 0 ? 'pagado' : 'pendiente', // Visualmente para la primera
            error: ''
        });
    }
    items.value = newItems;
};

// Validar fechas
const validarFechas = () => {
    let isValid = true;
    const hoy = new Date().toISOString().split('T')[0];

    // Fecha máxima: 30 días desde hoy (para todo el plan)
    const fechaLimite = new Date();
    fechaLimite.setDate(fechaLimite.getDate() + 30);
    const fechaLimiteStr = fechaLimite.toISOString().split('T')[0];

    items.value.forEach((item, index) => {
        item.error = '';

        // Validar rango fecha
        if (item.fecha < hoy) {
            item.error = 'Fecha no puede ser pasado';
            isValid = false;
        }
        if (item.fecha > fechaLimiteStr) {
            item.error = 'Máximo 30 días de plazo total';
            isValid = false;
        }

        // Validar orden
        if (index > 0) {
            const anterior = items.value[index - 1];
            if (item.fecha <= anterior.fecha) {
                item.error = 'Debe ser posterior a la cuota anterior';
                isValid = false;
            }
        }
    });
    return isValid;
};

watch(() => props.show, (val) => {
    if (val) {
        generarCuotas();
    }
});

watch(() => props.numeroCuotas, () => {
    if (props.show) generarCuotas();
});

const confirmar = () => {
    if (validarFechas()) {
        emit('confirm', items.value);
        emit('close');
    }
};
</script>

<template>
    <DialogModal :show="show" @close="emit('close')" :closeable="false">
        <template #title>
            Plan de Pagos ({{ numeroCuotas }} Cuotas)
        </template>

        <template #content>
            <div class="mb-4 bg-blue-50 dark:bg-blue-900/30 p-3 rounded-lg text-sm text-blue-700 dark:text-blue-200">
                <i class="fas fa-info-circle mr-2"></i>
                La <strong>Cuota 1</strong> debe pagarse ahora para confirmar el pedido.
                El resto debe completarse en un plazo máximo de <strong>30 días</strong>.
            </div>

            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10 rounded-lg">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">#
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Fecha
                                Vencimiento</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">Monto
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900 dark:text-gray-100">Estado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                        <tr v-for="(item, index) in items" :key="index">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-200">{{
                                item.numero }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                <div v-if="index === 0" class="font-bold text-gray-700 dark:text-gray-300">
                                    Hoy ({{ formatDate(item.fecha) }})
                                </div>
                                <div v-else>
                                    <input type="date" v-model="item.fecha" :min="items[index - 1]?.fecha"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 dark:bg-gray-800 dark:text-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <p v-if="item.error" class="text-red-500 text-xs mt-1">{{ item.error }}</p>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-gray-900 dark:text-gray-100 font-mono">
                                Bs {{ item.monto.toFixed(2) }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                <span v-if="index === 0"
                                    class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900/30 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20">Pago
                                    Inicial</span>
                                <span v-else
                                    class="inline-flex items-center rounded-md bg-yellow-50 dark:bg-yellow-900/30 px-2 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-400 ring-1 ring-inset ring-yellow-600/20">Pendiente</span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <td colspan="2" class="py-3 pl-4 pr-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">Total:
                            </td>
                            <td class="px-3 py-3 text-right text-sm font-bold text-gray-900 dark:text-gray-100">Bs {{ total.toFixed(2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="emit('close')">
                Cancelar
            </SecondaryButton>

            <button
                class="ml-3 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                @click="confirmar">
                Confirmar Plan
            </button>
        </template>
    </DialogModal>
</template>

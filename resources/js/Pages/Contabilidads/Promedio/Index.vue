<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

const props = defineProps({
    ingredientes: Array,
    kardex: Array,
    selectedIngrediente: Object,
    existenciaActual: Number,
    valorInventario: Number,
    filters: Object
});

const form = ref({
    ingrediente_id: props.filters.ingrediente_id || '',
    fecha_inicio: props.filters.fecha_inicio || '',
    fecha_fin: props.filters.fecha_fin || '',
});

const search = () => {
    router.get(route('inventario.metodo.promedio'), form.value, { preserveState: true });
};

const downloadPDF = () => {
    if (!props.kardex || props.kardex.length === 0) {
        alert('No hay datos para exportar');
        return;
    }
    try {
        const doc = new jsPDF();
        doc.setFontSize(16);
        doc.text(`Kardex Promedio Ponderado - ${props.selectedIngrediente?.nombre || 'Reporte'}`, 14, 15);
        doc.setFontSize(10);
        doc.text(`Unidad de Medida: ${props.selectedIngrediente?.unidad_medida || 'N/A'}`, 14, 22);
        doc.text(`Fecha de Reporte: ${new Date().toLocaleDateString()}`, 14, 28);

        const tableData = props.kardex.map(k => [
            new Date(k.fecha).toLocaleDateString(),
            k.tipo,
            String(k.cantidad),
            'Bs ' + Number(k.precio_unitario).toFixed(4),
            'Bs ' + Number(k.total).toFixed(2),
            String(k.existencia_lote),
            String(k.saldo_cantidad),
            'Bs ' + Number(k.saldo_total).toFixed(2)
        ]);

        const tableOptions = {
            head: [['Fecha', 'Mov.', 'Cant', 'Costo Prom', 'Total', 'Exist.', 'Saldo Q', 'Saldo $']],
            body: tableData,
            startY: 35,
            styles: { fontSize: 8, cellPadding: 2 },
            headStyles: { fillColor: [59, 130, 246], textColor: 255, fontStyle: 'bold' },
            alternateRowStyles: { fillColor: [219, 234, 254] },
            margin: { top: 35 }
        };

        if (typeof doc.autoTable === 'function') doc.autoTable(tableOptions);
        else if (typeof autoTable === 'function') autoTable(doc, tableOptions);
        else throw new Error('AutoTable no disponible');

        const finalY = (doc.lastAutoTable && doc.lastAutoTable.finalY) ? doc.lastAutoTable.finalY + 10 : doc.internal.pageSize.getHeight() - 30;
        doc.setFontSize(11);
        doc.setFont(undefined, 'bold');
        doc.text(`Existencia Total Actual: ${props.existenciaActual} ${props.selectedIngrediente?.unidad_medida || ''}`, 14, finalY);
        doc.text(`Valor de Inventario: Bs ${Number(props.valorInventario).toFixed(2)}`, 14, finalY + 7);

        doc.save(`kardex_promedio_${props.selectedIngrediente?.nombre || 'reporte'}_${new Date().toISOString().split('T')[0]}.pdf`);
    } catch (err) {
        console.error('Error generando PDF Promedio:', err);
        alert('No se pudo generar el PDF. Revisa la consola.');
    }
};
</script>

<template>
    <AppLayout title="Promedio Ponderado">
        <template #header>
            <div class="flex items-center gap-4">
                <button @click="router.get(route('metodos.index'))" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> Atrás
                </button>
                <h2 class="font-semibold text-xl text-blue-800 dark:text-blue-300 leading-tight">
                    <i class="fa-solid fa-balance-scale mr-2"></i> Promedio Ponderado
                </h2>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 transition-colors duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ingrediente</label>
                            <select v-model="form.ingrediente_id" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-lg focus:ring-blue-500">
                                <option value="">Seleccione...</option>
                                <option v-for="ing in ingredientes" :key="ing.id" :value="ing.id">{{ ing.nombre }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Desde</label>
                            <input type="date" v-model="form.fecha_inicio" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-lg focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hasta</label>
                            <input type="date" v-model="form.fecha_fin" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-white rounded-lg focus:ring-blue-500">
                        </div>
                        <div class="flex gap-2">
                            <button @click="search" class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 flex-1 transition-colors duration-300">Consultar</button>
                            <button v-if="kardex.length" @click="downloadPDF" class="bg-gray-600 dark:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors duration-300"><i class="fa-solid fa-file-pdf"></i></button>
                        </div>
                    </div>
                </div>

                <div v-if="selectedIngrediente" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700 transition-colors duration-300">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/30 border-b border-blue-100 dark:border-blue-800 flex justify-between items-center transition-colors duration-300">
                        <h3 class="font-bold text-blue-800 dark:text-blue-300">Kardex Promedio: {{ selectedIngrediente.nombre }}</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase font-medium transition-colors duration-300">
                                <tr>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Movimiento</th>
                                    <th class="px-6 py-3 text-center">Cant.</th>
                                    <th class="px-6 py-3 text-right">Costo Promedio</th>
                                    <th class="px-6 py-3 text-right">Total</th>
                                    <th class="px-6 py-3 text-center bg-blue-50 dark:bg-blue-900/20">Exist. Lote</th>
                                    <th class="px-6 py-3 text-center bg-blue-50 dark:bg-blue-900/20 font-bold">Saldo</th>
                                    <th class="px-6 py-3 text-right bg-blue-50 dark:bg-blue-900/20 font-bold">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 transition-colors duration-300">
                                <tr v-for="(row, idx) in kardex" :key="idx" 
                                    :class="row.tipo === 'ENTRADA' ? 'bg-green-50/30 dark:bg-green-900/10' : 'bg-red-50/30 dark:bg-red-900/10'"
                                    class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-300">
                                    <td class="px-6 py-3 text-gray-900 dark:text-gray-100">{{ new Date(row.fecha).toLocaleDateString() }}</td>
                                    <td class="px-6 py-3 font-bold" :class="row.tipo === 'ENTRADA' ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'">{{ row.tipo }}</td>
                                    <td class="px-6 py-3 text-center font-bold" :class="row.tipo === 'ENTRADA' ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400'">{{ row.tipo === 'ENTRADA' ? '+' : '-' }}{{ row.cantidad }}</td>
                                    <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100">Bs {{ Number(row.precio_unitario).toFixed(4) }}</td>
                                    <td class="px-6 py-3 text-right font-medium text-gray-900 dark:text-gray-100">Bs {{ Number(row.total).toFixed(2) }}</td>
                                    <td class="px-6 py-3 text-center bg-blue-50/50 dark:bg-blue-900/10 font-bold text-blue-700 dark:text-blue-400">{{ row.existencia_lote }}</td>
                                    <td class="px-6 py-3 text-center bg-blue-50/50 dark:bg-blue-900/10 font-bold text-gray-900 dark:text-gray-100">{{ row.saldo_cantidad }}</td>
                                    <td class="px-6 py-3 text-right bg-blue-50/50 dark:bg-blue-900/10 font-bold text-gray-900 dark:text-gray-100">Bs {{ Number(row.saldo_total).toFixed(2) }}</td>
                                </tr>
                                <tr v-if="kardex.length === 0">
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fa-solid fa-info-circle text-4xl mb-2 text-blue-300 dark:text-blue-600"></i>
                                        <p>Sin movimientos registrados.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="kardex.length > 0" class="bg-gradient-to-r from-blue-600 dark:from-blue-700 to-indigo-600 dark:to-indigo-700 p-6 text-white transition-colors duration-300">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center border-r border-white/30">
                                <p class="text-blue-100 dark:text-blue-200 text-sm uppercase tracking-wide mb-2">Existencia Total</p>
                                <p class="text-4xl font-bold">{{ existenciaActual }}</p>
                                <p class="text-blue-200 dark:text-blue-300 text-sm mt-1">{{ selectedIngrediente.unidad_medida }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-blue-100 dark:text-blue-200 text-sm uppercase tracking-wide mb-2">Valor de Inventario</p>
                                <p class="text-4xl font-bold">Bs {{ Number(valorInventario).toFixed(2) }}</p>
                                <p class="text-blue-200 dark:text-blue-300 text-sm mt-1">Promedio Ponderado</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
* {
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
}
</style>

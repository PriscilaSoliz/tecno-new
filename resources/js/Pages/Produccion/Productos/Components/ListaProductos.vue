<template>
	<div>
		<div class="p-6 border-b border-gray-200 flex flex-col md:flex-row gap-4 items-center">
			<div class="flex-1 relative">
				<input v-model="searchQuery" @input="emitSearch" placeholder="Buscar productos..."
					class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
				<i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
			</div>

			<div class="flex items-center gap-2">
				<select v-model="statusFilter" @change="emitFilter"
					class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
					<option value="">Todos</option>
					<option value="true">Activos</option>
					<option value="false">Inactivos</option>
				</select>
			</div>
		</div>

		<div class="overflow-x-auto">
			<table class="w-full">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							Imagen</th>
						<th v-for="col in columns" :key="col.field" @click="onSort(col.field)"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
							<div class="flex items-center">
								{{ col.label }}
								<i class="fas fa-sort ml-1 text-gray-400"></i>
							</div>
						</th>
						<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
							Acciones</th>
					</tr>
				</thead>

				<tbody class="bg-white divide-y divide-gray-200">
					<tr v-for="p in sorted" :key="p.id" class="hover:bg-gray-50 transition-colors">
						<td class="px-6 py-4">
							<div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 border">
								<img :src="getImageUrl(p.imagen)" :alt="p.nombre" class="w-full h-full object-cover"
									@error="handleImageError" />
							</div>
						</td>
						<td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ p.id }}</td>
						<td class="px-6 py-4 text-sm text-gray-900">{{ p.nombre }}</td>
						<td class="px-6 py-4 text-sm text-gray-900">{{ p.unidad_medida }}</td>
						<td class="px-6 py-4 text-sm">
							<p class="text-lg font-extrabold text-gray-900">Bs{{ Number(p.precio_venta).toFixed(2) }}
							</p>
						</td>
						<td class="px-6 py-4 whitespace-nowrap">
							<span
								:class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', p.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
								{{ p.is_active ? 'Activo' : 'Inactivo' }}
							</span>
						</td>
						<td class="px-6 py-4 text-sm">
							<div class="flex items-center space-x-3">
								<button @click="openPromoModal(p)"
									class="text-purple-600 hover:text-purple-900 font-semibold mr-2">Oferta</button>
								<button @click="$emit('edit', p)"
									class="text-blue-600 hover:text-blue-900">Editar</button>
								<button @click="$emit('toggle', p)"
									:class="p.is_active ? 'text-orange-600' : 'text-green-600'">
									{{ p.is_active ? 'Desactivar' : 'Activar' }}
								</button>
								<button @click="$emit('delete', p)"
									class="text-red-600 hover:text-red-900">Eliminar</button>
							</div>
						</td>
					</tr>

					<tr v-if="filtered.length === 0">
						<td colspan="8" class="px-6 py-8 text-center text-gray-500">
							<i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
							<p>No se encontraron productos</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<PromocionModal v-if="showPromoModal && productForPromo" :product="productForPromo" :show="showPromoModal"
			@close="closePromoModal" @updated="onPromoUpdated" />
	</div>
</template>

<script setup>
import { ref, computed } from 'vue';
import PromocionModal from '../PromocionModal.vue';

const props = defineProps({
	products: { type: Array, default: () => [] },
	initialSortField: { type: String, default: 'id' },
	initialSortDirection: { type: String, default: 'desc' },
});

const searchQuery = ref('');
const statusFilter = ref('');
const sortField = ref(props.initialSortField);
const sortDirection = ref(props.initialSortDirection);

const handleImageError = (e) => {
	if (!e.target.src.includes('placeholder')) {
		e.target.src = 'https://placehold.co/100x100/EEE/31343C?text=Producto';
	}
};

const getImageUrl = (img) => {
	if (!img) return 'https://placehold.co/100x100/EEE/31343C?text=Producto';
	if (typeof img !== 'string') return 'https://placehold.co/100x100/EEE/31343C?text=Producto';
	if (img.startsWith('http') || img.startsWith('/')) return img;
	return `/${img}`;
};

const emitSearch = () => { }
const emitFilter = () => { }

const filtered = computed(() => {
	const q = (searchQuery.value || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
	return props.products.filter(p => {
		const name = (p.nombre || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
		const desc = (p.descripcion || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
		const matchesSearch = !q || name.includes(q) || desc.includes(q);
		if (statusFilter.value !== '') {
			const required = statusFilter.value === 'true';
			return matchesSearch && p.is_active === required;
		}
		return matchesSearch;
	});
});

const sorted = computed(() => {
	return [...filtered.value].sort((a, b) => {
		let A = a[sortField.value], B = b[sortField.value];
		if (typeof A === 'string') { A = A.toLowerCase(); B = (B || '').toLowerCase(); }
		if (A < B) return sortDirection.value === 'asc' ? -1 : 1;
		if (A > B) return sortDirection.value === 'asc' ? 1 : -1;
		return 0;
	});
});

const onSort = (field) => {
	if (sortField.value === field) sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
	else { sortField.value = field; sortDirection.value = 'asc'; }
};

const columns = [
	{ field: 'id', label: 'ID' },
	{ field: 'nombre', label: 'Producto' },
	{ field: 'unidad_medida', label: 'Unidad' },
	{ field: 'precio_venta', label: 'Precio' },
	{ field: 'is_active', label: 'Estado' }
];

const showPromoModal = ref(false);
const productForPromo = ref(null);

const openPromoModal = (p) => {
	console.log('Opening Promo Modal for:', p);
	productForPromo.value = p;
	showPromoModal.value = true;
};
const closePromoModal = () => {
	productForPromo.value = null;
	showPromoModal.value = false;
};
const onPromoUpdated = () => {
	// Reload logic if needed, but the product list might not update automatically unless we refetch.
	// However, existing modals emit/reload.
	// For now we just close. The list is dynamic props. 
	// Ideally we emit an event to parent to reload data.
	// But parent passes props. Parent handles reloads?
	// Parent passes props.productos. We should probably emit an event 'promo-updated' 
	// or rely on inertia reload if modal does invalidation.
	// Our modal does logic. Let's redirect/reload in modal onSuccess?
	// Ah, modal uses form.put/post. That triggers Interia visit. Inertia reloads props automatically.
};
</script>
<style scoped>
/* compact styles */
</style>

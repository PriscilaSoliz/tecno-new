<template>
	<div>
		<div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row gap-4 items-center">
			<div class="flex-1 w-full sm:w-auto relative">
				<input v-model="searchQuery" @input="emitSearch" placeholder="Buscar productos..."
					class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
				<i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
			</div>

			<div class="flex items-center gap-2 w-full sm:w-auto">
				<select v-model="statusFilter" @change="emitFilter"
					class="w-full sm:w-auto border border-gray-300 rounded-lg px-3 py-2 text-sm">
					<option value="">Todos</option>
					<option value="true">Activos</option>
					<option value="false">Inactivos</option>
				</select>
			</div>
		</div>


		<!-- Vista de Tabla (Escritorio) -->
		<div class="hidden lg:block overflow-x-auto">
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
						<td class="px-6 py-4 text-center">
							<div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 border mx-auto">
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
						<td class="px-6 py-4 text-sm font-bold text-gray-700">
							<span :class="p.stock > 0 ? 'text-green-600' : 'text-red-600'">
								{{ p.stock ?? 0 }}
							</span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap">
							<span
								:class="['inline-flex px-2 py-1 text-xs font-semibold rounded-full', p.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
								{{ p.is_active ? 'Activo' : 'Inactivo' }}
							</span>
						</td>
						<td class="px-6 py-4 text-sm">
							<div class="flex items-center justify-center space-x-3">
								<button @click="openPromoModal(p)"
									class="text-purple-600 hover:text-purple-900 font-semibold mr-2 transition-all active:scale-95">Oferta</button>
								<button @click="$emit('edit', p)"
									class="text-blue-600 hover:text-blue-900 transition-all active:scale-95">Editar</button>
								<button @click="$emit('toggle', p)"
									:class="p.is_active ? 'text-orange-600 hover:text-orange-800' : 'text-green-600 hover:text-green-800'" class="transition-all active:scale-95">
									{{ p.is_active ? 'Desactivar' : 'Activar' }}
								</button>
								<button @click="$emit('delete', p)"
									class="text-red-600 hover:text-red-900 transition-all active:scale-95">Eliminar</button>
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

		<!-- Vista de Tarjetas (Móvil) -->
		<div class="lg:hidden p-4 grid grid-cols-1 sm:grid-cols-2 gap-6">
			<div v-for="p in sorted" :key="'card-' + p.id" 
				class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
				
				<!-- Imagen y Badges -->
				<div class="relative h-48 overflow-hidden bg-gray-50">
					<img :src="getImageUrl(p.imagen)" :alt="p.nombre" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
						@error="handleImageError" />
					<div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
					
					<div class="absolute top-4 left-4 flex gap-2">
						<span :class="p.is_active ? 'bg-emerald-500' : 'bg-rose-500'" class="px-3 py-1 text-white text-[10px] font-black uppercase rounded-full shadow-lg">
							{{ p.is_active ? 'Activo' : 'Inactivo' }}
						</span>
					</div>

					<div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-2xl shadow-lg border border-white/20">
						<span class="text-xs font-bold text-gray-900">#{{ p.id }}</span>
					</div>

					<div class="absolute bottom-4 left-4 right-4 flex justify-between items-end">
						<div class="bg-amber-600 px-4 py-2 rounded-2xl shadow-lg transform rotate-2">
							<p class="text-xs text-amber-100 font-bold leading-none mb-1">Precio</p>
							<p class="text-xl font-black text-white leading-none">Bs {{ Number(p.precio_venta).toFixed(2) }}</p>
						</div>
					</div>
				</div>

				<!-- Info del Producto -->
				<div class="p-6">
					<div class="mb-4">
						<h3 class="text-xl font-black text-gray-900 dark:text-white leading-tight mb-2 leading-none">{{ p.nombre }}</h3>
						<p class="text-xs text-gray-500 font-medium">Unidad: <span class="text-amber-600 font-bold">{{ p.unidad_medida }}</span></p>
					</div>

					<div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-4 mb-6 border border-gray-100 dark:border-gray-600 transition-colors">
						<div class="flex items-center justify-between">
							<div class="flex items-center gap-3">
								<div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
									<i class="fas fa-boxes-stacked text-blue-600"></i>
								</div>
								<div>
									<p class="text-[10px] text-gray-500 font-black uppercase tracking-widest">STOCK ACTUAL</p>
									<p :class="p.stock > 0 ? 'text-green-600' : 'text-rose-600'" class="text-base font-black">
										{{ p.stock ?? 0 }} <span class="text-[10px] font-bold text-gray-400">unidades</span>
									</p>
								</div>
							</div>
						</div>
					</div>

					<!-- Acciones Rápidas -->
					<div class="grid grid-cols-4 gap-2">
						<button @click="openPromoModal(p)"
							class="flex flex-col items-center justify-center p-3 rounded-2xl bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white transition-all active:scale-90 shadow-sm" title="Promoción">
							<i class="fas fa-tag mb-1"></i>
							<span class="text-[8px] font-bold uppercase">Oferta</span>
						</button>
						<button @click="$emit('edit', p)"
							class="flex flex-col items-center justify-center p-3 rounded-2xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all active:scale-90 shadow-sm" title="Editar">
							<i class="fas fa-edit mb-1"></i>
							<span class="text-[8px] font-bold uppercase">Editar</span>
						</button>
						<button @click="$emit('toggle', p)"
							:class="p.is_active ? 'bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white'"
							class="flex flex-col items-center justify-center p-3 rounded-2xl transition-all active:scale-90 shadow-sm" :title="p.is_active ? 'Desactivar' : 'Activar'">
							<i class="fas mb-1" :class="p.is_active ? 'fa-pause' : 'fa-play'"></i>
							<span class="text-[8px] font-bold uppercase">{{ p.is_active ? 'Off' : 'On' }}</span>
						</button>
						<button @click="$emit('delete', p)"
							class="flex flex-col items-center justify-center p-3 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all active:scale-90 shadow-sm" title="Eliminar">
							<i class="fas fa-trash-alt mb-1"></i>
							<span class="text-[8px] font-bold uppercase">Borrar</span>
						</button>
					</div>
				</div>
			</div>

			<div v-if="filtered.length === 0" class="col-span-full py-20 text-center">
				<i class="fas fa-search text-6xl text-gray-200 mb-4 scale-125 opactiy-50"></i>
				<h4 class="text-xl font-bold text-gray-400">No se encontraron productos</h4>
				<p class="text-gray-400 text-sm">Prueba ajustando tus términos de búsqueda o filtros.</p>
			</div>
		</div>

		<PromocionModal v-if="showPromoModal && productForPromo" :product="productForPromo" :show="showPromoModal"
			@close="closePromoModal" @updated="onPromoUpdated" />
	</div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
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
	const baseUrl = usePage().props.app_url || '';
	if (!img) return 'https://placehold.co/100x100/EEE/31343C?text=Producto';
	if (typeof img !== 'string') return 'https://placehold.co/100x100/EEE/31343C?text=Producto';
	if (img.startsWith('http')) return img;
	const cleanImg = img.startsWith('/') ? img.substring(1) : img;
	const cleanBase = baseUrl.endsWith('/') ? baseUrl : baseUrl + '/';
	return `${cleanBase}${cleanImg}`;
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
	{ field: 'stock', label: 'Stock' },
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

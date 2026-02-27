<template>
	<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
		<div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
			<div class="flex justify-between items-start">
				<div>
					<p class="text-purple-100">Próximas Entregas</p>
					<h3 class="text-2xl font-bold mt-1">{{ upcomingCount }} Ingredientes</h3>
					<p class="text-purple-100 text-sm mt-2">Para los próximos {{ upcomingDays }} días</p>
				</div>
				<div class="bg-white bg-opacity-20 p-3 rounded-xl">
					<i class="fas fa-calendar-day text-xl"></i>
				</div>
			</div>
			<div class="mt-4 flex items-center text-sm">
				<i class="fas fa-truck-loading mr-2"></i>
				<span>{{ upcomingDeliveriesText }}</span>
			</div>
		</div>

		<div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl shadow-lg p-6 text-white">
			<div class="flex justify-between items-start">
				<div>
					<p class="text-amber-100">Ingredientes por Caducar</p>
					<h3 class="text-2xl font-bold mt-1">{{ expiringCount }} Productos</h3>
					<p class="text-amber-100 text-sm mt-2">En los próximos {{ expiringDays }} días</p>
				</div>
				<div class="bg-white bg-opacity-20 p-3 rounded-xl">
					<i class="fas fa-hourglass-end text-xl"></i>
				</div>
			</div>
			<div class="mt-4 flex items-center text-sm">
				<i class="fas fa-clock mr-2"></i>
				<span>{{ expiringText }}</span>
			</div>
		</div>
	</div>
</template>

<script setup>
const props = defineProps({
	upcomingCount: { type: Number, default: 5 },
	upcomingDays: { type: Number, default: 3 },
	upcomingDeliveriesText: { type: String, default: '2 entregas programadas para mañana' },

	expiringCount: { type: Number, default: 8 },
	expiringDays: { type: Number, default: 7 },
	expiringText: { type: String, default: 'Revisar inventario de caducidades' },
});
</script>

<style scoped>
/* estilos compactos */
</style>

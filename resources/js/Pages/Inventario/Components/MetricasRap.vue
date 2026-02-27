<template>
	<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
		<div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
			<div class="flex items-center">
				<div class="p-3 bg-blue-100 rounded-lg mr-4">
					<i class="fas fa-boxes text-blue-600"></i>
				</div>
				<div>
					<p class="text-sm text-gray-600">Total Ingredientes</p>
					<h3 class="text-lg font-bold text-gray-800">{{ totalIngredients }}</h3>
				</div>
			</div>
		</div>

		<div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
			<div class="flex items-center">
				<div class="p-3 bg-green-100 rounded-lg mr-4">
					<i class="fas fa-truck text-green-600"></i>
				</div>
				<div>
					<p class="text-sm text-gray-600">Proveedores Activos</p>
					<h3 class="text-lg font-bold text-gray-800">{{ proveedoresActivos }}</h3>
				</div>
			</div>
		</div>

		<div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
			<div class="flex items-center">
				<div class="p-3 bg-red-100 rounded-lg mr-4">
					<i class="fas fa-exclamation-triangle text-red-600"></i>
				</div>
				<div>
					<p class="text-sm text-gray-600">Stock Crítico</p>
					<h3 class="text-lg font-bold text-gray-800">{{ stockCritico }}</h3>
				</div>
			</div>
		</div>

		<div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100">
			<div class="flex items-center">
				<div class="p-3 bg-purple-100 rounded-lg mr-4">
					<i class="fas fa-clock text-purple-600"></i>
				</div>
				<div>
					<p class="text-sm text-gray-600">Órdenes Pendientes</p>
					<h3 class="text-lg font-bold text-gray-800">{{ ordenesPendientes }}</h3>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
const props = defineProps({
	totalIngredients: { type: Number, default: 87 },
	proveedoresActivos: { type: Number, default: 15 },
	stockCritico: { type: Number, default: 5 },
	ordenesPendientes: { type: Number, default: 3 },
});
</script>

<style scoped>
/* mantener estilo minimal */
</style>

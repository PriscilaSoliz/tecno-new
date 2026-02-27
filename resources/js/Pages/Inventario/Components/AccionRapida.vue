<template>
	<div class="mt-8 bg-gray-50 rounded-xl p-6 border border-gray-200">
		<h3 class="text-lg font-semibold text-gray-800 mb-4">Acciones Rápidas</h3>
		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
			<button @click="$emit('new-ingrediente')" class="flex items-center justify-center p-4 bg-white rounded-lg shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors">
				<i class="fas fa-plus-circle text-green-600 mr-3"></i>
				<span class="text-gray-700">Nuevo Ingrediente</span>
			</button>
			<button @click="$emit('register-proveedor')" class="flex items-center justify-center p-4 bg-white rounded-lg shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors">
				<i class="fas fa-truck text-blue-600 mr-3"></i>
				<span class="text-gray-700">Registrar Proveedor</span>
			</button>
			<button @click="$emit('new-orden')" class="flex items-center justify-center p-4 bg-white rounded-lg shadow-sm border border-gray-200 hover:bg-gray-50 transition-colors">
				<i class="fas fa-clipboard-list text-purple-600 mr-3"></i>
				<span class="text-gray-700">Nueva Orden Compra</span>
			</button>
		</div>
	</div>
</template>

<script setup>
// no props, solo emite eventos
</script>

<style scoped>
/* pequeño estilo local */
</style>

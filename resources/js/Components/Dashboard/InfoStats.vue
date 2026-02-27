<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-blue-100">Balance General</p>
          <h3 class="text-2xl font-bold mt-1">$15,430.00</h3>
        </div>
        <div class="bg-white bg-opacity-20 p-3 rounded-xl">
          <i class="fas fa-chart-line text-xl"></i>
        </div>
      </div>
      <div class="mt-4 flex items-center text-sm">
        <i class="fas fa-arrow-up mr-1"></i>
        <span>12% más que el mes anterior</span>
      </div>
    </div>

    <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl shadow-lg p-6 text-white">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-purple-100">Clientes Activos</p>
          <h3 class="text-2xl font-bold mt-1">342</h3>
        </div>
        <div class="bg-white bg-opacity-20 p-3 rounded-xl">
          <i class="fas fa-users text-xl"></i>
        </div>
      </div>
      <div class="mt-4 flex items-center text-sm">
        <i class="fas fa-user-plus mr-1"></i>
        <span>+15 este mes</span>
      </div>
    </div>

    <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl shadow-lg p-6 text-white">
      <div class="flex justify-between items-start">
        <div>
          <p class="text-amber-100">Productos en Stock</p>
          <h3 class="text-2xl font-bold mt-1">1,248</h3>
        </div>
        <div class="bg-white bg-opacity-20 p-3 rounded-xl">
          <i class="fas fa-boxes text-xl"></i>
        </div>
      </div>
      <div class="mt-4 flex items-center text-sm">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        <span>12 productos con stock bajo</span>
      </div>
    </div>
  </div>
</template>

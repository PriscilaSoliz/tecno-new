<script setup>
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
const props = defineProps({
  order: { type: Object, required: true },
  products: { type: Array, default: () => [] },
  operarios: { type: Array, default: () => [] },
});
const emit = defineEmits(['close','updated']);

const form = useForm({
  producto_id: props.order.producto_id,
  cantidad_a_producir: props.order.cantidad_a_producir,
  operario_id: props.order.operario_id || null,
  fecha_creacion: props.order.fecha_creacion ? props.order.fecha_creacion.split(' ')[0] : ''
});

watch(() => props.order, (o) => {
  form.producto_id = o.producto_id;
  form.cantidad_a_producir = o.cantidad_a_producir;
  form.operario_id = o.operario_id;
  form.fecha_creacion = o.fecha_creacion ? o.fecha_creacion.split(' ')[0] : '';
});

const submit = () => {
  form.put(route('ordenes.update', props.order.id), {
    preserveScroll: true,
    onSuccess: () => emit('updated'),
  });
};

const close = () => emit('close');
</script>

<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg">
      <div class="px-6 py-4 border-b flex justify-between">
        <h3 class="font-semibold">Editar Orden #{{ order.id }}</h3>
        <button @click="close" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
      </div>

      <form @submit.prevent="submit" class="p-6 space-y-4">
        <div>
          <label class="block text-sm">Producto</label>
          <select v-model="form.producto_id" class="w-full border px-3 py-2 rounded">
            <option v-for="p in props.products" :key="p.id" :value="p.id">{{ p.nombre }}</option>
          </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm">Cantidad</label>
            <input type="number" min="1" v-model="form.cantidad_a_producir" class="w-full border rounded px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm">Operario</label>
            <select v-model="form.operario_id" class="w-full border px-3 py-2 rounded">
              <option value="">-- sin asignar --</option>
              <option v-for="o in props.operarios" :key="o.id" :value="o.id">{{ o.turno }} - {{ o.especialidad }}</option>
            </select>
          </div>
        </div>

        <div class="flex justify-end gap-3">
          <button type="button" @click="close" class="px-4 py-2 border rounded">Cancelar</button>
          <button class="px-4 py-2 bg-blue-600 text-white rounded">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()
const errors = computed(() => page.props.errors || {})

const form = reactive({
  title: '',
  description: '',
  ciclo_requerido: '',
  modalidad: 'presencial',
  ubicacion: '',
})

function submit(e) {
  e.preventDefault()
  router.post('/vacantes', form)
}
</script>

<template>
  <div class="p-6 max-w-xl space-y-4">
    <h1 class="text-2xl font-bold">Crear vacante</h1>

    <div v-if="Object.keys(errors).length" class="bg-red-50 border border-red-200 p-3 text-sm">
      <div v-for="(msg, field) in errors" :key="field" class="text-red-700">• {{ msg }}</div>
    </div>

    <form @submit="submit" class="space-y-3">
      <input v-model="form.title" class="border p-2 w-full" placeholder="Título" required>
      <textarea v-model="form.description" class="border p-2 w-full" placeholder="Descripción"></textarea>
      <input v-model="form.ciclo_requerido" class="border p-2 w-full" placeholder="Ciclo requerido (DAW...)" required>
      <select v-model="form.modalidad" class="border p-2 w-full" required>
        <option value="presencial">Presencial</option>
        <option value="híbrido">Híbrido</option>
        <option value="remoto">Remoto</option>
      </select>
      <input v-model="form.ubicacion" class="border p-2 w-full" placeholder="Ubicación">
      <button class="bg-black text-white px-4 py-2">Guardar</button>
    </form>
  </div>
</template>

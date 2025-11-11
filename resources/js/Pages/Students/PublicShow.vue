<script setup>
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  student: { type: Object, required: true },
})
</script>

<template>
  <Head :title="props.student.name || 'Alumno'" />

  <div class="min-h-[70vh] mx-auto max-w-3xl p-6">
    <div class="rounded-2xl border bg-white/90 p-6 shadow-sm
                dark:border-gray-800 dark:bg-gray-900/80 dark:text-gray-100">
      <h1 class="text-2xl font-semibold">{{ props.student.name }}</h1>

      <p v-if="props.student.email" class="mt-2 text-sm text-gray-600 dark:text-gray-300">
        {{ props.student.email }}
      </p>
      <p v-else class="mt-2 text-sm text-gray-500 dark:text-gray-400">
        (Inicia sesión para ver más información)
      </p>
    </div>
  </div>
</template>

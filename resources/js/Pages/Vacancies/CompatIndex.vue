<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  items: { type: Array, default: () => [] } // [{id,title,company,city,province,mode,score}]
})

const formatMode = (m) => m==='remote' ? 'Remota' : m==='hybrid' ? 'Híbrida' : 'Presencial'
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Vacantes compatibles" />

    <div class="mx-auto max-w-6xl p-6">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
        Vacantes (≥ 50% de compatibilidad)
      </h1>

      <div v-if="!props.items.length" class="mt-6 rounded-2xl bg-white p-6 text-gray-600 shadow dark:bg-gray-800 dark:text-gray-300">
        No hay vacantes que superen el 50% ahora mismo.
      </div>

      <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
        <div v-for="v in props.items" :key="v.id" class="rounded-2xl bg-white p-5 shadow dark:bg-gray-800">
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ v.title }}</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ v.company?.trade_name || v.company?.legal_name || 'Empresa' }}
              </p>
            </div>
            <span class="rounded-full bg-indigo-600 px-3 py-1 text-sm font-semibold text-white">
              {{ v.score }}%
            </span>
          </div>

          <div class="mt-3 flex flex-wrap gap-2 text-xs text-gray-700 dark:text-gray-300">
            <span v-if="v.city || v.province" class="rounded-full bg-gray-100 px-3 py-1 dark:bg-gray-900">
              {{ v.city }}<span v-if="v.city && v.province"> — </span>{{ v.province }}
            </span>
            <span v-if="v.mode" class="rounded-full bg-gray-100 px-3 py-1 dark:bg-gray-900">
              {{ formatMode(v.mode) }}
            </span>
          </div>

          <div class="mt-4">
            <Link :href="route('vacancies.show', v.id)"
                  class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
              Ver más
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

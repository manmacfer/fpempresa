<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  vacancies: { type: Object, required: true }, // paginator
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Mis vacantes" />

    <div class="max-w-6xl mx-auto p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Mis vacantes</h1>
        <Link :href="route('vacancies.create')" class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
          Crear vacante
        </Link>
      </div>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="v in vacancies.data" :key="v.id"
             class="rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-5 shadow">
          <div class="flex items-start justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ v.title }}</h3>
            <span class="text-xs rounded-full px-2 py-0.5"
                  :class="v.status==='published' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' :
                          v.status==='closed' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200' :
                          'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200'">
              {{ v.status }}
            </span>
          </div>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">{{ v.description }}</p>

          <dl class="mt-3 text-xs text-gray-600 dark:text-gray-300 space-y-1">
            <div v-if="v.cycle_required">Ciclo: <b>{{ v.cycle_required }}</b></div>
            <div v-if="v.mode">Modalidad: <b>{{ v.mode }}</b></div>
            <div v-if="v.city || v.province">Ubicación: <b>{{ v.city }} <span v-if="v.city && v.province">—</span> {{ v.province }}</b></div>
          </dl>

          <div class="mt-4 flex items-center justify-between">
            <Link :href="`/vacantes/${v.id}`" class="text-indigo-600 hover:underline dark:text-indigo-400">Ver</Link>
            <!-- aquí luego pondremos Editar/Cerrar -->
          </div>
        </div>
      </div>

      <!-- Paginación simple -->
      <div class="mt-6 flex items-center justify-center gap-2">
        <Link v-for="link in vacancies.links" :key="link.url || link.label" :href="link.url || '#'"
              v-html="link.label"
              class="px-3 py-1 rounded-lg border text-sm"
              :class="[
                link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300',
                !link.url ? 'opacity-40 pointer-events-none' : ''
              ]" />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

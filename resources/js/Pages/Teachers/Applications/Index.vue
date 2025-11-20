<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  applications: Object,
  filters: Object
})

const searchQuery = ref(props.filters?.search || '')
const selectedState = ref(props.filters?.state || '')

const states = [
  { value: '', label: 'Todos los estados' },
  { value: 'enviada', label: 'Enviada' },
  { value: 'vista', label: 'Vista' },
  { value: 'entrevista', label: 'En entrevista' },
  { value: 'rechazada', label: 'Rechazada' },
  { value: 'aceptada', label: 'Aceptada' }
]

// Aplicar filtros
const applyFilters = () => {
  router.get(route('teacher.applications.index'), {
    search: searchQuery.value,
    state: selectedState.value
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

// Limpiar filtros
const clearFilters = () => {
  searchQuery.value = ''
  selectedState.value = ''
  router.get(route('teacher.applications.index'))
}

const hasActiveFilters = computed(() => {
  return searchQuery.value || selectedState.value
})

const getStateLabel = (state) => {
  const labels = {
    enviada: 'Enviada',
    vista: 'Vista',
    entrevista: 'En entrevista',
    rechazada: 'Rechazada',
    aceptada: 'Aceptada'
  }
  return labels[state] || state
}

const getStateColor = (state) => {
  const colors = {
    enviada: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
    vista: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200',
    entrevista: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
    rechazada: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200',
    aceptada: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200'
  }
  return colors[state] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-200'
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Todas las Candidaturas" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
            Todas las Candidaturas
          </h1>
          <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            Supervisa todas las candidaturas de tus alumnos
          </p>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Búsqueda -->
            <div>
              <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Buscar
              </label>
              <input
                id="search"
                v-model="searchQuery"
                type="text"
                placeholder="Alumno o vacante..."
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @keyup.enter="applyFilters"
              />
            </div>

            <!-- Filtro por estado -->
            <div>
              <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Estado
              </label>
              <select
                id="state"
                v-model="selectedState"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="applyFilters"
              >
                <option v-for="state in states" :key="state.value" :value="state.value">
                  {{ state.label }}
                </option>
              </select>
            </div>

            <!-- Botones -->
            <div class="flex items-end gap-2">
              <button
                @click="applyFilters"
                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200"
              >
                Aplicar
              </button>
              <button
                v-if="hasActiveFilters"
                @click="clearFilters"
                class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-lg transition-colors duration-200"
              >
                Limpiar
              </button>
            </div>
          </div>
        </div>

        <!-- Lista de candidaturas -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
          <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
              Candidaturas ({{ applications.total }})
            </h2>

            <!-- Sin resultados -->
            <div v-if="applications.data.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                No se encontraron candidaturas
              </p>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Intenta ajustar los filtros de búsqueda
              </p>
            </div>

            <!-- Lista de candidaturas -->
            <div v-else class="space-y-4">
              <div
                v-for="application in applications.data"
                :key="application.id"
                class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
              >
                <!-- Avatar del alumno -->
                <div class="flex-shrink-0">
                  <img
                    v-if="application.student.avatar_path"
                    :src="`/storage/${application.student.avatar_path}`"
                    :alt="`${application.student.first_name} ${application.student.last_name}`"
                    class="h-14 w-14 rounded-xl object-cover shadow-md ring-2 ring-white dark:ring-gray-800"
                  />
                  <div
                    v-else
                    class="h-14 w-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md ring-2 ring-white dark:ring-gray-800"
                  >
                    <span class="text-xl font-bold text-white">
                      {{ (application.student.first_name || '?')[0].toUpperCase() }}
                    </span>
                  </div>
                </div>

                <!-- Información -->
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ application.student.first_name }} {{ application.student.last_name }}
                  </p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    aplicó a <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ application.vacancy.title }}</span>
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-500">
                    {{ application.vacancy.company?.trade_name || application.vacancy.company?.legal_name }}
                    · {{ new Date(application.created_at).toLocaleDateString('es-ES') }}
                  </p>
                </div>

                <!-- Estado -->
                <div class="flex-shrink-0">
                  <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', getStateColor(application.state)]">
                    {{ getStateLabel(application.state) }}
                  </span>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-shrink-0 items-center gap-2">
                  <Link
                    :href="route('students.public.show', application.student.id)"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200"
                  >
                    Ver Alumno
                  </Link>
                  
                  <Link
                    :href="route('vacancies.show', application.vacancy.id)"
                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors duration-200"
                  >
                    Ver Vacante
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Paginación -->
          <div v-if="applications.data.length > 0" class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-900/50">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Mostrando
                <span class="font-medium">{{ applications.from }}</span>
                a
                <span class="font-medium">{{ applications.to }}</span>
                de
                <span class="font-medium">{{ applications.total }}</span>
                resultados
              </div>

              <div class="flex gap-2">
                <component
                  :is="link.url ? Link : 'span'"
                  v-for="(link, index) in applications.links"
                  :key="index"
                  :href="link.url || undefined"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200',
                    link.active
                      ? 'bg-indigo-600 text-white'
                      : link.url
                      ? 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 border border-gray-300 dark:border-gray-600'
                      : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed'
                  ]"
                  v-html="link.label"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

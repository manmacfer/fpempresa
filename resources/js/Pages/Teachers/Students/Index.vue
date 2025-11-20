<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  students: Object,
  cycles: Array,
  filters: Object
})

const searchQuery = ref(props.filters?.search || '')
const selectedCycle = ref(props.filters?.cycle || '')

// Aplicar filtros
const applyFilters = () => {
  router.get(route('teacher.students.index'), {
    search: searchQuery.value,
    cycle: selectedCycle.value
  }, {
    preserveState: true,
    preserveScroll: true
  })
}

// Limpiar filtros
const clearFilters = () => {
  searchQuery.value = ''
  selectedCycle.value = ''
  router.get(route('teacher.students.index'))
}

const hasActiveFilters = computed(() => {
  return searchQuery.value || selectedCycle.value
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Mis Alumnos" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
            Mis Alumnos
          </h1>
          <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            Gestiona y supervisa el progreso de tus estudiantes
          </p>
        </div>

        <!-- Filtros -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Búsqueda -->
            <div>
              <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Buscar alumno
              </label>
              <input
                id="search"
                v-model="searchQuery"
                type="text"
                placeholder="Nombre, apellido..."
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @keyup.enter="applyFilters"
              />
            </div>

            <!-- Filtro por ciclo -->
            <div>
              <label for="cycle" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Ciclo Formativo
              </label>
              <select
                id="cycle"
                v-model="selectedCycle"
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="applyFilters"
              >
                <option value="">Todos los ciclos</option>
                <option v-for="cycle in cycles" :key="cycle" :value="cycle">
                  {{ cycle }}
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

        <!-- Lista de alumnos -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
          <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
              Alumnos ({{ students.total }})
            </h2>

            <!-- Sin resultados -->
            <div v-if="students.data.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <p class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                No se encontraron alumnos
              </p>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Intenta ajustar los filtros de búsqueda
              </p>
            </div>

            <!-- Tarjetas de alumnos -->
            <div v-else class="space-y-4">
              <div
                v-for="student in students.data"
                :key="student.id"
                class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
              >
                <!-- Avatar -->
                <div class="flex-shrink-0">
                  <img
                    v-if="student.avatar_path"
                    :src="`/storage/${student.avatar_path}`"
                    :alt="`${student.first_name} ${student.last_name}`"
                    class="h-14 w-14 rounded-xl object-cover shadow-md ring-2 ring-white dark:ring-gray-800"
                  />
                  <div
                    v-else
                    class="h-14 w-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md ring-2 ring-white dark:ring-gray-800"
                  >
                    <span class="text-xl font-bold text-white">
                      {{ (student.first_name || '?')[0].toUpperCase() }}
                    </span>
                  </div>
                </div>

                <!-- Información del alumno -->
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ student.first_name }} {{ student.last_name }}
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ student.cycle || 'Sin ciclo' }}
                    <span v-if="student.fp_modality"> · {{ student.fp_modality }}</span>
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ student.city || 'Sin ubicación' }}<span v-if="student.province">, {{ student.province }}</span>
                  </p>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-shrink-0 items-center gap-2">
                  <Link
                    :href="route('students.public.show', student.id)"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200"
                  >
                    Perfil
                  </Link>

                  <Link
                    :href="route('teacher.students.applications', student.id)"
                    class="inline-flex items-center rounded-lg border border-blue-300 bg-white px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-50 dark:border-blue-700 dark:bg-gray-700 dark:text-blue-400 dark:hover:bg-blue-900/20 transition-colors duration-200"
                  >
                    Candidaturas
                  </Link>

                  <Link
                    :href="route('teacher.students.matches', student.id)"
                    class="inline-flex items-center rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 transition-colors duration-200"
                  >
                    Matchings
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Paginación -->
          <div v-if="students.data.length > 0" class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-900/50">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-700 dark:text-gray-300">
                Mostrando
                <span class="font-medium">{{ students.from }}</span>
                a
                <span class="font-medium">{{ students.to }}</span>
                de
                <span class="font-medium">{{ students.total }}</span>
                resultados
              </div>

              <div class="flex gap-2">
                <component
                  :is="link.url ? Link : 'span'"
                  v-for="(link, index) in students.links"
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

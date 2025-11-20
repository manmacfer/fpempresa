<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  student: Object,
  matches: Array
})

const expandedMatch = ref(null)

const toggleMatch = (index) => {
  expandedMatch.value = expandedMatch.value === index ? null : index
}

const getScoreColor = (score) => {
  if (score >= 80) return 'text-green-600 dark:text-green-400'
  if (score >= 60) return 'text-blue-600 dark:text-blue-400'
  if (score >= 40) return 'text-yellow-600 dark:text-yellow-400'
  return 'text-red-600 dark:text-red-400'
}

const getScoreBgColor = (score) => {
  if (score >= 80) return 'bg-green-100 dark:bg-green-900/30'
  if (score >= 60) return 'bg-blue-100 dark:bg-blue-900/30'
  if (score >= 40) return 'bg-yellow-100 dark:bg-yellow-900/30'
  return 'bg-red-100 dark:bg-red-900/30'
}

const getCategoryLabel = (key) => {
  const labels = {
    cycle_fp: 'Ciclo y tipo de FP',
    availability: 'Disponibilidad',
    modality: 'Modalidad de trabajo',
    location: 'Ubicación',
    tech: 'Tecnologías',
    languages: 'Idiomas'
  }
  return labels[key] || key
}

const getMaxPoints = (key) => {
  const maxPoints = {
    cycle_fp: 25,
    availability: 5,
    modality: 10,
    location: 10,
    tech: 40,
    languages: 10
  }
  return maxPoints[key] || 100
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Matchings de ${student.first_name} ${student.last_name}`" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header con info del alumno -->
        <div class="mb-8">
          <Link
            :href="route('teacher.my-students')"
            class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 mb-4"
          >
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Volver a Mis Alumnos
          </Link>

          <div class="flex items-center gap-4">
            <!-- Avatar -->
            <div class="flex-shrink-0">
              <img
                v-if="student.avatar_path"
                :src="`/storage/${student.avatar_path}`"
                :alt="`${student.first_name} ${student.last_name}`"
                class="h-20 w-20 rounded-2xl object-cover shadow-lg ring-4 ring-white dark:ring-gray-800"
              />
              <div
                v-else
                class="h-20 w-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg ring-4 ring-white dark:ring-gray-800"
              >
                <span class="text-3xl font-bold text-white">
                  {{ (student.first_name || '?')[0].toUpperCase() }}
                </span>
              </div>
            </div>

            <!-- Info -->
            <div>
              <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                {{ student.first_name }} {{ student.last_name }}
              </h1>
              <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                {{ student.cycle || 'Sin ciclo' }}
                <span v-if="student.fp_modality"> · {{ student.fp_modality }}</span>
              </p>
              <p class="text-sm text-gray-500 dark:text-gray-500">
                {{ matches.length }} vacantes compatibles
              </p>
            </div>
          </div>
        </div>

        <!-- Sin matches -->
        <div v-if="matches.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-12 text-center">
          <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">
            No hay vacantes compatibles
          </h3>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            Este alumno no tiene vacantes que coincidan con su perfil actual.
          </p>
        </div>

        <!-- Lista de matches -->
        <div v-else class="space-y-4">
          <div
            v-for="(match, index) in matches"
            :key="match.vacancy.id"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden transition-all duration-200 hover:shadow-xl"
          >
            <!-- Cabecera del match -->
            <div class="p-6">
              <div class="flex items-start justify-between gap-4">
                <!-- Info de la vacante -->
                <div class="flex-1 min-w-0">
                  <Link
                    :href="route('vacancies.show', match.vacancy.id)"
                    class="text-xl font-bold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200"
                  >
                    {{ match.vacancy.title }}
                  </Link>
                  
                  <p class="mt-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ match.vacancy.company?.trade_name || match.vacancy.company?.legal_name }}
                  </p>
                  
                  <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-600 dark:text-gray-400">
                    <span v-if="match.vacancy.cycle_required" class="inline-flex items-center gap-1">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                      </svg>
                      {{ match.vacancy.cycle_required }}
                    </span>
                    
                    <span v-if="match.vacancy.city" class="inline-flex items-center gap-1">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      {{ match.vacancy.city }}<span v-if="match.vacancy.province">, {{ match.vacancy.province }}</span>
                    </span>
                    
                    <span v-if="match.vacancy.mode" class="inline-flex items-center gap-1">
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                      {{ match.vacancy.mode }}
                    </span>
                  </div>
                </div>

                <!-- Score -->
                <div class="flex-shrink-0 text-center">
                  <div :class="['inline-flex items-center justify-center w-20 h-20 rounded-2xl', getScoreBgColor(match.score)]">
                    <div>
                      <div :class="['text-3xl font-bold', getScoreColor(match.score)]">
                        {{ match.score }}
                      </div>
                      <div class="text-xs font-medium text-gray-600 dark:text-gray-400">
                        / 100
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Botón para expandir desglose -->
              <button
                @click="toggleMatch(index)"
                class="mt-4 w-full flex items-center justify-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors duration-200"
              >
                <span>{{ expandedMatch === index ? 'Ocultar' : 'Ver' }} desglose de compatibilidad</span>
                <svg
                  :class="['h-4 w-4 transition-transform duration-200', expandedMatch === index ? 'rotate-180' : '']"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
            </div>

            <!-- Desglose de compatibilidad -->
            <div v-if="expandedMatch === index" class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 p-6">
              <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">
                Desglose de Compatibilidad
              </h4>
              
              <div class="space-y-3">
                <div
                  v-for="(value, key) in match.breakdown"
                  :key="key"
                  v-show="key !== 'raw'"
                  class="flex items-center gap-3"
                >
                  <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                      <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ getCategoryLabel(key) }}
                      </span>
                      <span class="text-sm font-bold text-gray-900 dark:text-white">
                        {{ value }} puntos
                      </span>
                    </div>
                    
                    <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                      <div
                        :style="{ width: `${(value / getMaxPoints(key)) * 100}%` }"
                        :class="[
                          'h-full rounded-full transition-all duration-300',
                          value >= getMaxPoints(key) * 0.8 ? 'bg-green-500' :
                          value >= getMaxPoints(key) * 0.5 ? 'bg-blue-500' :
                          value >= getMaxPoints(key) * 0.3 ? 'bg-yellow-500' : 'bg-red-500'
                        ]"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

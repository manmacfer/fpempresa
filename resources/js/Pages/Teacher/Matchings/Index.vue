<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  matchings: Array
})

const getStatusBadge = (matching) => {
  if (matching.validado_centro) {
    return { text: 'Validado', color: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' }
  }
  if (matching.student_matched && matching.company_matched) {
    return { text: 'Pendiente validación', color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200' }
  }
  return { text: 'Incompleto', color: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-200' }
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Matchings Pendientes" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
            Matchings Pendientes de Validación
          </h1>
          <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            Revisa y valida los matchings completados entre alumnos y empresas
          </p>
        </div>

        <!-- Info box -->
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
          <div class="flex items-start gap-3">
            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div class="text-sm text-blue-800 dark:text-blue-200">
              <p class="font-medium mb-1">¿Qué son los matchings?</p>
              <p>Un matching se completa cuando tanto el alumno como la empresa han confirmado su interés mutuo. Como tutor, debes revisar cada matching y validarlo para que puedan comenzar las prácticas.</p>
            </div>
          </div>
        </div>

        <!-- Sin matchings -->
        <div v-if="matchings.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-12 text-center">
          <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">
            No hay matchings pendientes
          </h3>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            Cuando un alumno y una empresa completen un matching, aparecerá aquí para tu revisión.
          </p>
        </div>

        <!-- Lista de matchings -->
        <div v-else class="space-y-4">
          <div
            v-for="matching in matchings"
            :key="matching.id"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden hover:shadow-xl transition-shadow duration-200"
          >
            <div class="p-6">
              <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                      {{ matching.vacancy?.title || 'Sin título' }}
                    </h3>
                    <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', getStatusBadge(matching).color]">
                      {{ getStatusBadge(matching).text }}
                    </span>
                  </div>
                  
                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Empresa: <span class="font-medium text-gray-900 dark:text-white">{{ matching.company?.trade_name || matching.company?.legal_name }}</span>
                  </p>
                </div>
              </div>

              <!-- Información del matching -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Alumno -->
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                  <img
                    v-if="matching.student?.avatar_path"
                    :src="`/storage/${matching.student.avatar_path}`"
                    :alt="matching.student.first_name"
                    class="h-12 w-12 rounded-full object-cover ring-2 ring-white dark:ring-gray-800"
                  />
                  <div
                    v-else
                    class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center ring-2 ring-white dark:ring-gray-800"
                  >
                    <span class="text-lg font-bold text-white">
                      {{ (matching.student?.first_name || '?')[0].toUpperCase() }}
                    </span>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                      {{ matching.student?.first_name }} {{ matching.student?.last_name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ matching.student?.cycle || 'Sin ciclo' }}
                    </p>
                  </div>
                </div>

                <!-- Vacante info -->
                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                  <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 mb-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ matching.vacancy?.city }}<span v-if="matching.vacancy?.province">, {{ matching.vacancy.province }}</span></span>
                  </div>
                  <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="capitalize">{{ matching.vacancy?.mode || 'No especificado' }}</span>
                  </div>
                </div>
              </div>

              <!-- Acciones -->
              <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <Link
                  :href="route('teacher.matchings.show', matching.id)"
                  class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Ver Detalle
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

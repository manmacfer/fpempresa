<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  student: Object,
  applications: Array
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
    <Head :title="`Candidaturas de ${student.first_name} ${student.last_name}`" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
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

          <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
            Candidaturas de {{ student.first_name }} {{ student.last_name }}
          </h1>
          <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            {{ applications.length }} candidatura{{ applications.length !== 1 ? 's' : '' }} enviada{{ applications.length !== 1 ? 's' : '' }}
          </p>
        </div>

        <!-- Sin candidaturas -->
        <div v-if="applications.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-12 text-center">
          <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <h3 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">
            Sin candidaturas
          </h3>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            Este alumno aún no ha enviado candidaturas a vacantes.
          </p>
        </div>

        <!-- Lista de candidaturas -->
        <div v-else class="space-y-4">
          <div
            v-for="application in applications"
            :key="application.id"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6 hover:shadow-xl transition-shadow duration-200"
          >
            <div class="flex items-start justify-between gap-4">
              <!-- Info de la vacante -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-2">
                  <Link
                    :href="route('vacancies.show', application.vacancy.id)"
                    class="text-xl font-bold text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200"
                  >
                    {{ application.vacancy.title }}
                  </Link>
                  
                  <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', getStateColor(application.state)]">
                    {{ getStateLabel(application.state) }}
                  </span>
                </div>
                
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {{ application.vacancy.company?.trade_name || application.vacancy.company?.legal_name }}
                </p>
                
                <div class="flex flex-wrap gap-3 text-xs text-gray-600 dark:text-gray-400">
                  <span v-if="application.vacancy.cycle_required" class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    {{ application.vacancy.cycle_required }}
                  </span>
                  
                  <span v-if="application.vacancy.city" class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ application.vacancy.city }}<span v-if="application.vacancy.province">, {{ application.vacancy.province }}</span>
                  </span>
                  
                  <span class="inline-flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ new Date(application.created_at).toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                  </span>
                </div>

                <!-- Feedback (si existe) -->
                <div v-if="application.feedback" class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                  <p class="text-sm font-medium text-blue-900 dark:text-blue-200 mb-1">
                    Feedback de la empresa:
                  </p>
                  <p class="text-sm text-blue-800 dark:text-blue-300">
                    {{ application.feedback }}
                  </p>
                </div>
              </div>

              <!-- Acciones -->
              <div class="flex-shrink-0">
                <Link
                  :href="route('vacancies.show', application.vacancy.id)"
                  class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200"
                >
                  Ver vacante
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

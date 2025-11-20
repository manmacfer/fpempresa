<template>
  <AuthenticatedLayout>
    <Head title="Candidaturas a mis Vacantes" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <!-- Flash messages -->
        <div v-if="page.props.flash?.success" class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 p-4 shadow-sm dark:border-green-900/50 dark:from-green-900/30 dark:to-emerald-900/20">
          <svg class="h-5 w-5 shrink-0 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ page.props.flash.success }}</p>
        </div>
        <div v-if="page.props.flash?.error" class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-gradient-to-r from-red-50 to-pink-50 p-4 shadow-sm dark:border-red-900/50 dark:from-red-900/30 dark:to-pink-900/20">
          <svg class="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ page.props.flash.error }}</p>
        </div>

        <!-- Header -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Candidaturas recibidas</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
              Gestiona las solicitudes de los estudiantes a tus vacantes
            </p>
          </div>
          <Link
            :href="route('vacancies.my')"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Mis Vacantes
          </Link>
        </div>

        <div class="space-y-6">
        <div v-if="vacancies.length === 0" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
          <div class="px-6 py-16 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
              <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">No hay candidaturas</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              No tienes vacantes publicadas o aún no has recibido solicitudes.
            </p>
          </div>
        </div>

        <!-- Tarjeta por vacante -->
        <div v-else v-for="vacancy in vacancies" :key="vacancy.id" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
          <!-- Título de la vacante -->
          <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-5 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                  {{ vacancy.title }}
                </h2>
                <p class="mt-1 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-2m-3-3.87a4 4 0 11-6 0m6 0a4 4 0 11-6 0m6 0V21m-6 0V9a3 3 0 116 0V21m-6 0H4m10 0h3"/>
                  </svg>
                  {{ vacancy.applications.length }} candidatura{{ vacancy.applications.length !== 1 ? 's' : '' }}
                </p>
              </div>
              <Link
                :href="route('vacancies.show', vacancy.id)"
                class="inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-white/50 px-4 py-2 text-sm font-semibold text-indigo-700 backdrop-blur-sm transition-all hover:bg-white dark:border-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Ver vacante
              </Link>
            </div>
          </div>

          <!-- Lista de candidatos -->
          <div v-if="vacancy.applications.length === 0" class="px-6 py-8 text-center">
            <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
              <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
              </svg>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              No hay candidaturas para esta vacante.
            </p>
          </div>

          <ul v-else class="divide-y divide-gray-100 dark:divide-gray-800">
            <li v-for="app in vacancy.applications" :key="app.id" class="px-6 py-5 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <div class="flex items-center gap-4">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                  <img
                    v-if="app.avatar_url"
                    :src="app.avatar_url"
                    :alt="app.name"
                    class="h-14 w-14 rounded-xl object-cover shadow-md ring-2 ring-white dark:ring-gray-800"
                  />
                  <div
                    v-else
                    class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 shadow-md ring-2 ring-white dark:from-indigo-900/50 dark:to-purple-900/50 dark:ring-gray-800"
                  >
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-300">
                      {{ app.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                </div>

                <!-- Información del alumno -->
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ app.name }}
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ app.cycle }}
                    <span v-if="app.fp_modality"> · {{ app.fp_modality }}</span>
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ app.city }}<span v-if="app.province">, {{ app.province }}</span>
                  </p>
                </div>

                <!-- Estado -->
                <div class="flex-shrink-0">
                  <span :class="statusBadgeClass(app.state)">
                    {{ statusLabel(app.state) }}
                  </span>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-shrink-0 items-center gap-2">
                  <Link
                    :href="route('students.public.show', app.student_id)"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600"
                  >
                    Perfil
                  </Link>

                  <button
                    v-if="!['rechazada', 'aceptada'].includes(app.state)"
                    @click="rejectApplication(app.id)"
                    :disabled="rejectForm.processing"
                    class="inline-flex items-center rounded-lg border border-red-300 bg-white px-3 py-1.5 text-sm font-medium text-red-700 hover:bg-red-50 dark:border-red-700 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20 disabled:opacity-50"
                  >
                    Rechazar
                  </button>

                  <button
                    v-if="!['rechazada', 'aceptada'].includes(app.state)"
                    @click="acceptApplication(app.id)"
                    :disabled="acceptForm.processing"
                    class="inline-flex items-center rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 disabled:opacity-50"
                  >
                    Match
                  </button>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, usePage, useForm } from '@inertiajs/vue3'

const page = usePage()

defineProps({
  vacancies: {
    type: Array,
    default: () => []
  }
})

const statusLabel = (state) => {
  if (!state) return 'En proceso'
  const s = String(state).toLowerCase()
  if (s === 'rechazada') return 'Rechazada'
  if (s === 'aceptada') return 'Match'
  if (s === 'enviada') return 'Nueva'
  if (s === 'vista') return 'Vista'
  if (s === 'entrevista') return 'En entrevista'
  return state
}

const statusBadgeClass = (state) => {
  const s = String(state || '').toLowerCase()
  if (s === 'aceptada') {
    return 'inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-200'
  }
  if (s === 'rechazada') {
    return 'inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-200'
  }
  if (s === 'enviada') {
    return 'inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-200'
  }
  return 'inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-900 dark:text-gray-300'
}

const rejectForm = useForm({})
const rejectApplication = (applicationId) => {
  if (!confirm('¿Rechazar esta candidatura? El alumno no podrá volver a postularse a esta vacante.')) return
  
  rejectForm.patch(route('applications.reject', applicationId), {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Error al rechazar:', errors)
      alert('Error al rechazar la candidatura')
    }
  })
}

const acceptForm = useForm({})
const acceptApplication = (applicationId) => {
  if (!confirm('¿Hacer Match con este alumno? Se eliminarán sus otras candidaturas y la vacante quedará bloqueada.')) return
  
  acceptForm.patch(route('applications.accept', applicationId), {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Error al aceptar:', errors)
      alert('Error al hacer Match')
    }
  })
}
</script>
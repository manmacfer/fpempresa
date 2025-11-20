<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  stats: { type: Object, required: true }
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Panel de Administración" />

    <div class="mx-auto max-w-7xl p-6">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
          Panel de Administración
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Gestiona usuarios, vacantes y matchings desde este panel
        </p>
      </div>

      <!-- Stats Grid -->
      <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Total Usuarios -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Usuarios</p>
              <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                {{ stats.total_users }}
              </p>
            </div>
            <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30">
              <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 flex gap-4 text-sm">
            <span class="text-gray-600 dark:text-gray-400">
              👨‍🎓 {{ stats.total_students }} Alumnos
            </span>
            <span class="text-gray-600 dark:text-gray-400">
              🏢 {{ stats.total_companies }} Empresas
            </span>
            <span class="text-gray-600 dark:text-gray-400">
              👨‍🏫 {{ stats.total_teachers }} Profesores
            </span>
          </div>
        </div>

        <!-- Vacantes -->
        <Link :href="route('admin.vacancies.index')" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 transition hover:shadow-lg hover:border-green-300 dark:hover:border-green-700 cursor-pointer">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Vacantes Totales</p>
              <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                {{ stats.total_vacancies }}
              </p>
            </div>
            <div class="rounded-full bg-green-100 p-3 dark:bg-green-900/30">
              <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
          </div>
          <div class="mt-4">
            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-200">
              {{ stats.active_vacancies }} activas
            </span>
          </div>
        </Link>

        <!-- Matchings -->
        <Link :href="route('admin.matchings.index')" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 transition hover:shadow-lg hover:border-purple-300 dark:hover:border-purple-700 cursor-pointer">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Matchings</p>
              <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                {{ stats.total_matchings }}
              </p>
            </div>
            <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/30">
              <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4">
            <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-200">
              {{ stats.validated_matchings }} validados
            </span>
          </div>
        </Link>

        <!-- Candidaturas Pendientes -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Candidaturas Sin Match</p>
              <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">
                {{ stats.pending_applications }}
              </p>
            </div>
            <div class="rounded-full bg-yellow-100 p-3 dark:bg-yellow-900/30">
              <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
          <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
            Candidaturas que aún no han generado matching
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Acciones Rápidas</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <Link
            :href="route('admin.users.create')"
            class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
          >
            <div class="rounded-full bg-indigo-100 p-2 dark:bg-indigo-900/30">
              <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900 dark:text-gray-100">Crear Usuario</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Profesor, alumno o empresa</p>
            </div>
          </Link>

          <Link
            :href="route('admin.users.index', { type: 'student' })"
            class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
          >
            <div class="rounded-full bg-blue-100 p-2 dark:bg-blue-900/30">
              <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900 dark:text-gray-100">Ver Alumnos</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Gestionar alumnos</p>
            </div>
          </Link>

          <Link
            :href="route('admin.users.index', { type: 'company' })"
            class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
          >
            <div class="rounded-full bg-green-100 p-2 dark:bg-green-900/30">
              <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900 dark:text-gray-100">Ver Empresas</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Gestionar empresas</p>
            </div>
          </Link>

          <Link
            :href="route('admin.users.index', { type: 'teacher' })"
            class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
          >
            <div class="rounded-full bg-orange-100 p-2 dark:bg-orange-900/30">
              <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900 dark:text-gray-100">Ver Profesores</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Gestionar profesores</p>
            </div>
          </Link>

          <Link
            :href="route('admin.matchings.index')"
            class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
          >
            <div class="rounded-full bg-purple-100 p-2 dark:bg-purple-900/30">
              <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900 dark:text-gray-100">Ver Matchings</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Gestionar matchings</p>
            </div>
          </Link>

          <Link
            :href="route('admin.vacancies.index')"
            class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800"
          >
            <div class="rounded-full bg-pink-100 p-2 dark:bg-pink-900/30">
              <svg class="h-5 w-5 text-pink-600 dark:text-pink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900 dark:text-gray-100">Ver Vacantes</p>
              <p class="text-sm text-gray-600 dark:text-gray-400">Gestionar vacantes</p>
            </div>
          </Link>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  role: { type: String, default: null }
})

const page = usePage()

const effectiveRole = computed(() => props.role ?? page.props?.auth?.role ?? '—')

// Nombre del usuario
const displayName = computed(() => page.props?.auth?.name ?? 'Usuario')

// Rol en español
const roleSpanish = computed(() => {
  const role = effectiveRole.value
  if (role === 'student' || role === 'students') return 'Alumno'
  if (role === 'company' || role === 'companies') return 'Empresa'
  if (role === 'teacher' || role === 'teachers') return 'Profesor'
  if (role === 'admin') return 'Administrador'
  return role
})

// Es empresa
const isCompany = computed(() => {
  const role = effectiveRole.value
  return role === 'company' || role === 'companies'
})

// Es profesor
const isTeacher = computed(() => {
  const role = effectiveRole.value
  return role === 'teacher' || role === 'teachers'
})

function safeRoute(name, params = {}, fallback = '/vacancies') {
  return (typeof route === 'function') ? route(name, params) : fallback
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">Dashboard</h1>
          <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            Hola de nuevo, 
            <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ displayName }}</span>
          </p>
        </div>

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Alumnos Card (solo profesores) -->
          <Link
            v-if="isTeacher"
            :href="safeRoute('teacher.my-students', {}, '/teacher/my-students')"
            class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-lg border border-gray-100 dark:border-gray-700/50
                   hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
          >
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-full blur-2xl
                        group-hover:scale-150 transition-transform duration-500"></div>
            
            <div class="relative">
              <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 shadow-lg
                          group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
              </div>
              
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Mis Alumnos</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Gestiona y supervisa a tus estudiantes
              </p>
              
              <div class="flex items-center text-indigo-600 dark:text-indigo-400 font-medium text-sm group-hover:gap-2 transition-all">
                <span>Ver mis alumnos</span>
                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </div>
            </div>
          </Link>

          <!-- Vacantes Card -->
          <Link
            v-if="!isTeacher"
            :href="isCompany ? safeRoute('vacancies.create', {}, '/vacancies/create') : safeRoute('vacancies.index', {}, '/vacancies')"
            class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-lg border border-gray-100 dark:border-gray-700/50
                   hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
          >
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 rounded-full blur-2xl
                        group-hover:scale-150 transition-transform duration-500"></div>
            
            <div class="relative">
              <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 shadow-lg
                          group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Vacantes</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                <template v-if="isCompany">Publica nuevas ofertas de empleo</template>
                <template v-else>Explora y gestiona las ofertas disponibles</template>
              </p>
              
              <div class="flex items-center text-indigo-600 dark:text-indigo-400 font-medium text-sm group-hover:gap-2 transition-all">
                <span v-if="isCompany">Ve a crear tu vacante</span>
                <span v-else>Ver todas</span>
                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </div>
            </div>
          </Link>

          <!-- Matchings Card -->
          <Link
            v-if="!isTeacher"
            :href="isCompany ? safeRoute('matching.company', {}, '/matching') : safeRoute('matching.student', {}, '/matching')"
            class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-lg border border-gray-100 dark:border-gray-700/50
                   hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
          >
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-full blur-2xl
                        group-hover:scale-150 transition-transform duration-500"></div>
            
            <div class="relative">
              <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-4 shadow-lg
                          group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Matchings</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Revisa tus conexiones y candidaturas
              </p>
              
              <div class="flex items-center text-green-600 dark:text-green-400 font-medium text-sm group-hover:gap-2 transition-all">
                <span>Ver matchings</span>
                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </div>
            </div>
          </Link>

          <!-- Perfil Card -->
          <Link
            v-if="!isTeacher"
            :href="safeRoute('students.edit.me', {}, '/profile')"
            class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 p-6 shadow-lg border border-gray-100 dark:border-gray-700/50
                   hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1"
          >
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/10 to-cyan-500/10 rounded-full blur-2xl
                        group-hover:scale-150 transition-transform duration-500"></div>
            
            <div class="relative">
              <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mb-4 shadow-lg
                          group-hover:scale-110 transition-transform duration-300">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
              
              <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Mi Perfil</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Actualiza tu información personal
              </p>
              
              <div class="flex items-center text-blue-600 dark:text-blue-400 font-medium text-sm group-hover:gap-2 transition-all">
                <span>Editar perfil</span>
                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </div>
            </div>
          </Link>
        </div>

        <!-- Stats Section (opcional) -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Estado</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">Activo</p>
              </div>
              <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rol</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white capitalize">{{ roleSpanish }}</p>
              </div>
              <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
              </div>
            </div>
          </div>

          <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700/50">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Plataforma</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">FP</p>
              </div>
              <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

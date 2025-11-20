<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import ToggleThemeButton from '@/Components/ToggleThemeButton.vue'
import NotificationDropdown from '@/Components/NotificationDropdown.vue'

const page = usePage()
const showingNavigationDropdown = ref(false)
const showingUserDropdown = ref(false)

// IDs que nos llegan desde Inertia
const studentId = computed(() => page.props.auth?.studentId)
const companyId = computed(() => page.props.auth?.companyId)

// ---- Normalización de rol ----
const rawRole = computed(() => page.props.auth?.roleSlug ?? page.props.auth?.role ?? null)
const normalize = (v) => {
  if (!v) return null
  const s = String(v).toLowerCase().trim()
  if (['student','students','alumno','alumna','alumnos','alumnas','estudiante','estudiantes'].includes(s)) return 'student'
  if (['company','companies','empresa','empresas','compania','compañia','compañía'].includes(s)) return 'company'
  if (['teacher','teachers','profesor','profesora','profesores','profesoras','tutor','tutora'].includes(s)) return 'teacher'
  return s
}
const role = computed(() => {
  const n = normalize(rawRole.value)
  if (n) return n
  if (companyId.value && !studentId.value) return 'company'
  if (studentId.value && !companyId.value) return 'student'
  return 'student'
})

// Nombre corto mostrado
const displayName = computed(() => {
  return (page.props.auth?.user?.name ?? page.props.auth?.name ?? 'Usuario').toString().trim()
})

const firstName = computed(() => {
  const n = displayName.value
  return n.split(' ')[0] || n
})

// Foto del usuario
const userPhoto = computed(() => {
  // Intentar obtener foto de diferentes fuentes
  const auth = page.props.auth || {}
  
  // Estudiante: avatar_url (accessor completo)
  if (auth.student?.avatar_url) {
    return auth.student.avatar_url
  }
  
  // Estudiante: avatar_path (ruta relativa)
  if (auth.student?.avatar_path) {
    return `/storage/${auth.student.avatar_path}`
  }
  
  // Empresa: logo_path
  if (auth.company?.logo_path) {
    return `/storage/${auth.company.logo_path}`
  }
  
  // Usuario directo: photo o avatar
  if (auth.user?.photo) {
    return `/storage/${auth.user.photo}`
  }
  
  if (auth.user?.avatar) {
    return `/storage/${auth.user.avatar}`
  }
  
  // Directamente en auth
  if (auth.photo) {
    return `/storage/${auth.photo}`
  }
  
  if (auth.avatar) {
    return `/storage/${auth.avatar}`
  }
  
  return null
})

// Rutas
const myEditHref = computed(() =>
  role.value === 'company' ? route('companies.edit.me') : route('students.edit.me')
)

const myPublicHref = computed(() => {
  if (role.value === 'company' && companyId.value) {
    return route('companies.public.show', companyId.value)
  }
  if (role.value === 'student' && studentId.value) {
    return route('students.public.show', studentId.value)
  }
  return null
})
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-950">
    <!-- NAVBAR -->
    <nav class="sticky top-0 z-50 border-b border-gray-200/50 bg-white/80 shadow-sm backdrop-blur-lg dark:border-gray-800/50 dark:bg-gray-900/80">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <!-- IZQUIERDA: logo + links -->
          <div class="flex items-center gap-6">
            <Link :href="route('dashboard')" class="group flex items-center gap-2">
              <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 shadow-md transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
              </div>
              <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-lg font-bold text-transparent dark:from-indigo-400 dark:to-purple-400">FP Empresa</span>
            </Link>

            <!-- PROFESOR: Mis Alumnos -->
            <Link
              v-if="role==='teacher'"
              :href="route('teacher.my-students')"
              class="group relative text-sm rounded-xl px-4 py-2 font-semibold transition-all duration-300
                     text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
              :class="{ 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 dark:from-indigo-950/30 dark:to-purple-950/30 dark:text-indigo-400': route().current('teacher.my-students') || route().current('teacher.students.*') }"
            >
              <span class="relative z-10 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Mis Alumnos
              </span>
              <span class="absolute inset-0 scale-0 rounded-xl bg-gradient-to-r from-indigo-600/10 to-purple-600/10 transition-transform duration-300 group-hover:scale-100"></span>
            </Link>

            <!-- PROFESOR: Candidaturas -->
            <Link
              v-if="role==='teacher'"
              :href="route('teacher.applications.index')"
              class="group relative text-sm rounded-xl px-4 py-2 font-semibold transition-all duration-300
                     text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
              :class="{ 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 dark:from-indigo-950/30 dark:to-purple-950/30 dark:text-indigo-400': route().current('teacher.applications.*') }"
            >
              <span class="relative z-10 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Candidaturas
              </span>
              <span class="absolute inset-0 scale-0 rounded-xl bg-gradient-to-r from-indigo-600/10 to-purple-600/10 transition-transform duration-300 group-hover:scale-100"></span>
            </Link>

            <!-- PROFESOR: Matchings por validar -->
            <Link
              v-if="role==='teacher'"
              :href="route('teacher.matchings.index')"
              class="group relative text-sm rounded-xl px-4 py-2 font-semibold transition-all duration-300
                     text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
              :class="{ 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 dark:from-indigo-950/30 dark:to-purple-950/30 dark:text-indigo-400': route().current('teacher.matchings.*') }"
            >
              <span class="relative z-10 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Matchings
              </span>
              <span class="absolute inset-0 scale-0 rounded-xl bg-gradient-to-r from-indigo-600/10 to-purple-600/10 transition-transform duration-300 group-hover:scale-100"></span>
            </Link>

            <!-- ADMIN: Panel de Administración -->
            <Link
              v-if="role==='admin'"
              :href="route('admin.dashboard')"
              class="group relative text-sm rounded-xl px-4 py-2 font-semibold transition-all duration-300
                     text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
              :class="{ 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 dark:from-indigo-950/30 dark:to-purple-950/30 dark:text-indigo-400': route().current('admin.*') }"
            >
              <span class="relative z-10 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Admin
              </span>
              <span class="absolute inset-0 scale-0 rounded-xl bg-gradient-to-r from-indigo-600/10 to-purple-600/10 transition-transform duration-300 group-hover:scale-100"></span>
            </Link>

            <!-- TODOS: Zona de Seguimiento -->
            <Link
              v-if="$page.props.auth.user"
              :href="route('seguimiento.index')"
              class="group relative text-sm rounded-xl px-4 py-2 font-semibold transition-all duration-300
                     text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
              :class="{ 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 dark:from-indigo-950/30 dark:to-purple-950/30 dark:text-indigo-400': route().current('seguimiento.*') }"
            >
              <span class="relative z-10 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Seguimiento
              </span>
              <span class="absolute inset-0 scale-0 rounded-xl bg-gradient-to-r from-indigo-600/10 to-purple-600/10 transition-transform duration-300 group-hover:scale-100"></span>
            </Link>

            <!-- Vacantes SOLO para alumnos -->
            <Link
              v-if="role==='student'"
              :href="route('vacancies.index')"
              class="group relative text-sm rounded-xl px-4 py-2 font-semibold transition-all duration-300
                     text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
              :class="{ 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 dark:from-indigo-950/30 dark:to-purple-950/30 dark:text-indigo-400': route().current('vacancies.index') }"
            >
              <span class="relative z-10 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Vacantes
              </span>
              <span class="absolute inset-0 scale-0 rounded-xl bg-gradient-to-r from-indigo-600/10 to-purple-600/10 transition-transform duration-300 group-hover:scale-100"></span>
            </Link>

            <!-- Matchings para alumnos y empresas (el que ya existía) -->
            <Link
              v-if="(role==='student' || role==='company') && route().has && route().has('matchings.index')"
              :href="route('matchings.index')"
              class="group relative text-sm rounded-xl px-4 py-2 font-semibold transition-all duration-300
                     text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
              :class="{ 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 dark:from-indigo-950/30 dark:to-purple-950/30 dark:text-indigo-400': route().current('matchings.index') }"
            >
              <span class="relative z-10 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Matchings
              </span>
              <span class="absolute inset-0 scale-0 rounded-xl bg-gradient-to-r from-indigo-600/10 to-purple-600/10 transition-transform duration-300 group-hover:scale-100"></span>
            </Link>

            <!-- SOLO COMPANY: Crear/Mis vacantes -->
            <template v-if="role==='company'">
              <Link
                :href="route('vacancies.create')"
                class="group relative text-sm rounded-xl px-4 py-2 font-semibold transition-all duration-300
                       text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
                :class="{ 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 dark:from-indigo-950/30 dark:to-purple-950/30 dark:text-indigo-400': route().current('vacancies.create') }"
              >
                <span class="relative z-10 flex items-center gap-2">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                  </svg>
                  Crear vacante
                </span>
                <span class="absolute inset-0 scale-0 rounded-xl bg-gradient-to-r from-indigo-600/10 to-purple-600/10 transition-transform duration-300 group-hover:scale-100"></span>
              </Link>

              <Link
                :href="route('vacancies.my')"
                class="group relative text-sm rounded-xl px-4 py-2 font-semibold transition-all duration-300
                       text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400"
                :class="{ 'bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-600 dark:from-indigo-950/30 dark:to-purple-950/30 dark:text-indigo-400': route().current('vacancies.my') }"
              >
                <span class="relative z-10 flex items-center gap-2">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                  </svg>
                  Mis vacantes
                </span>
                <span class="absolute inset-0 scale-0 rounded-xl bg-gradient-to-r from-indigo-600/10 to-purple-600/10 transition-transform duration-300 group-hover:scale-100"></span>
              </Link>
            </template>
          </div>

          <!-- DERECHA: tema + notificaciones + usuario -->
          <div class="hidden items-center gap-4 sm:flex">
            <ToggleThemeButton class="rounded-xl p-2 hover:bg-gray-100 dark:hover:bg-gray-800" />
            
            <!-- Campanita de notificaciones -->
            <NotificationDropdown />

            <!-- Dropdown de usuario -->
            <div class="relative">
              <button
                @click="showingUserDropdown = !showingUserDropdown"
                class="group flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold transition-all duration-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-950/30 dark:hover:to-purple-950/30"
              >
                <!-- Con foto -->
                <img
                  v-if="userPhoto"
                  :src="userPhoto"
                  :alt="firstName"
                  class="h-8 w-8 rounded-full object-cover shadow-md ring-2 ring-indigo-600/20 transition-all duration-300 group-hover:scale-110 group-hover:ring-indigo-600/50"
                />
                <!-- Sin foto: inicial -->
                <div
                  v-else
                  class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 text-sm font-bold text-white shadow-md transition-all duration-300 group-hover:scale-110"
                >
                  {{ firstName[0].toUpperCase() }}
                </div>
                <span class="text-gray-700 group-hover:text-indigo-600 dark:text-gray-300 dark:group-hover:text-indigo-400">{{ firstName }}</span>
                <svg
                  class="h-4 w-4 transition-transform duration-200"
                  :class="{ 'rotate-180': showingUserDropdown }"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <!-- Menú desplegable -->
              <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 scale-95"
                enter-to-class="opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div
                  v-show="showingUserDropdown"
                  class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-xl border border-gray-200 bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none dark:border-gray-700 dark:bg-gray-800"
                  @click.away="showingUserDropdown = false"
                >
                  <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ firstName }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ role === 'teacher' ? 'Profesor' : role === 'student' ? 'Estudiante' : role === 'company' ? 'Empresa' : 'Administrador' }}</p>
                  </div>

                  <div class="py-1">
                    <!-- Perfil público (si existe) -->
                    <Link
                      v-if="myPublicHref"
                      :href="myPublicHref"
                      class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-indigo-50 dark:text-gray-300 dark:hover:bg-indigo-950/30"
                    >
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      Ver perfil público
                    </Link>

                    <!-- Editar perfil (NO profesores) -->
                    <Link
                      v-if="role !== 'teacher' && myEditHref"
                      :href="myEditHref"
                      class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-indigo-50 dark:text-gray-300 dark:hover:bg-indigo-950/30"
                    >
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                      Editar perfil
                    </Link>
                  </div>

                  <div class="border-t border-gray-200 py-1 dark:border-gray-700">
                    <!-- Cerrar sesión -->
                    <Link
                      v-if="route().has && route().has('logout')"
                      :href="route('logout')"
                      method="post"
                      as="button"
                      class="flex w-full items-center gap-3 px-4 py-2 text-left text-sm text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                      </svg>
                      Cerrar sesión
                    </Link>
                  </div>
                </div>
              </Transition>
            </div>
          </div>

          <!-- MÓVIL: hamburguesa -->
          <div class="-me-2 flex items-center sm:hidden">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center rounded-md p-2 text-gray-400
                     hover:bg-gray-100 hover:text-gray-500 focus:outline-none dark:hover:bg-gray-800"
              aria-label="Abrir menú"
            >
              <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path
                  :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"
                />
                <path
                  :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- MENÚ MÓVIL -->
      <div class="sm:hidden" :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }">
        <div class="flex items-center justify-between px-4 pb-2 pt-3">
          <span class="text-sm text-gray-600 dark:text-gray-300">Tema</span>
          <ToggleThemeButton />
        </div>

        <div class="space-y-1 pb-3 pt-2">
          <!-- Vacantes SOLO alumnos en móvil -->
          <Link
            v-if="role==='student'"
            :href="route('vacancies.index')"
            class="block ps-3 pe-4 py-2 text-base font-medium
                   text-gray-600 hover:bg-gray-50 hover:text-gray-800
                   dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
          >
            Vacantes
          </Link>

          <Link
            v-if="route().has && route().has('matchings.index')"
            :href="route('matchings.index')"
            class="block ps-3 pe-4 py-2 text-base font-medium
                   text-gray-600 hover:bg-gray-50 hover:text-gray-800
                   dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
          >
            Matchings
          </Link>

          <Link
            :href="myEditHref"
            class="block ps-3 pe-4 py-2 text-base font-medium
                   text-gray-600 hover:bg-gray-50 hover:text-gray-800
                   dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
          >
            Mi perfil
          </Link>

          <!-- SOLO COMPANY en móvil -->
          <template v-if="role==='company'">
            <Link
              :href="route('vacancies.create')"
              class="block ps-3 pe-4 py-2 text-base font-medium
                     text-gray-600 hover:bg-gray-50 hover:text-gray-800
                     dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
            >
              Crear vacante
            </Link>

            <Link
              :href="route('vacancies.my')"
              class="block ps-3 pe-4 py-2 text-base font-medium
                     text-gray-600 hover:bg-gray-50 hover:text-gray-800
                     dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
            >
              Mis vacantes
            </Link>
          </template>
        </div>

        <div class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-800">
          <div class="px-4">
            <div class="text-base font-medium text-gray-800 dark:text-gray-100">
              <Link v-if="myPublicHref" :href="myPublicHref" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                {{ displayName }}
              </Link>
              <span v-else>{{ displayName }}</span>
            </div>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
              {{ page.props.auth?.user?.email || '' }}
            </div>
          </div>

          <div class="mt-3 space-y-1 px-4">
            <Link
              v-if="route().has && route().has('profile.edit')"
              :href="route('profile.edit')"
              class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
            >
              Cuenta
            </Link>
            <Link
              v-if="route().has && route().has('logout')"
              :href="route('logout')"
              method="post"
              as="button"
              class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-gray-100"
            >
              Cerrar sesión
            </Link>
          </div>
        </div>
      </div>
    </nav>

    <!-- HEADER -->
    <header v-if="$slots.header" class="bg-white shadow dark:bg-gray-900 dark:shadow-none">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- CONTENIDO -->
    <main>
      <slot />
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import ToggleThemeButton from '@/Components/ToggleThemeButton.vue'

const page = usePage()
const showingNavigationDropdown = ref(false)

// IDs que nos llegan desde Inertia
const studentId = computed(() => page.props.auth?.studentId)
const companyId = computed(() => page.props.auth?.companyId)

// ---- Normalización de rol ----
// Acepta roleSlug (si usas tabla roles) o role (legacy string)
// y normaliza alias: alumno/alumnos/students -> student; empresa/companies -> company
const rawRole = computed(() => page.props.auth?.roleSlug ?? page.props.auth?.role ?? null)
const normalize = (v) => {
  if (!v) return null
  const s = String(v).toLowerCase().trim()
  if (['student','students','alumno','alumna','alumnos','alumnas','estudiante','estudiantes'].includes(s)) return 'student'
  if (['company','companies','empresa','empresas','compania','compañia','compañía'].includes(s)) return 'company'
  return s // por si usas admin/teacher u otros
}
const role = computed(() => {
  const n = normalize(rawRole.value)
  if (n) return n
  // Deducción de emergencia por IDs (si el campo role viniera vacío)
  if (companyId.value && !studentId.value) return 'company'
  if (studentId.value && !companyId.value) return 'student'
  // por defecto mantenemos student (como tenías)
  return 'student'
})

// Nombre corto mostrado
const firstName = computed(() => {
  const n = (page.props.auth?.user?.name ?? 'Usuario').toString().trim()
  return n.split(' ')[0] || n
})

// Rutas (mantengo tu uso de Ziggy `route()`)
const myEditHref = computed(() =>
  role.value === 'company' ? route('companies.edit.me') : route('students.edit.me')
)

const myPublicHref = computed(() => {
  if (role.value === 'company') {
    return companyId.value ? route('companies.public.show', companyId.value) : null
  }
  return studentId.value ? route('students.public.show', studentId.value) : null
})
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- NAVBAR -->
    <nav class="border-b border-gray-100 bg-white dark:border-gray-800 dark:bg-gray-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <!-- IZQUIERDA: logo + links -->
          <div class="flex items-center gap-6">
            <Link :href="route('dashboard')" class="font-semibold text-gray-900 dark:text-gray-100">
              FP Empresa
            </Link>

            <Link
              :href="route('dashboard')"
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:bg-gray-50 hover:text-gray-900
                     dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
              :class="{ 'bg-gray-100 dark:bg-gray-800': route().current('dashboard') }"
            >
              Dashboard
            </Link>

            <Link
              :href="route('vacancies.index')"
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:bg-gray-50 hover:text-gray-900
                     dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
              :class="{ 'bg-gray-100 dark:bg-gray-800': route().current('vacancies.index') }"
            >
              Vacantes
            </Link>

            <Link
              v-if="route().has && route().has('matchings.index')"
              :href="route('matchings.index')"
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:bg-gray-50 hover:text-gray-900
                     dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
              :class="{ 'bg-gray-100 dark:bg-gray-800': route().current('matchings.index') }"
            >
              Matchings
            </Link>

            <!-- Mi perfil (según rol) -->
            <Link
              :href="myEditHref"
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:bg-gray-50 hover:text-gray-900
                     dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
              :class="{
                'bg-gray-100 dark:bg-gray-800':
                  route().current('students.edit.me') || route().current('companies.edit.me')
              }"
            >
              Mi perfil
            </Link>
          </div>

          <!-- DERECHA: tema + usuario -->
          <div class="hidden items-center gap-3 sm:flex">
            <ToggleThemeButton class="me-1" />

            <!-- Nombre → perfil público si existe id -->
            <Link
              v-if="myPublicHref"
              :href="myPublicHref"
              class="text-sm text-gray-600 hover:underline dark:text-gray-300"
            >
              {{ firstName }}
            </Link>
            <span v-else class="text-sm text-gray-600 dark:text-gray-300">
              {{ firstName }}
            </span>

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
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:bg-gray-50 hover:text-gray-900
                     dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
            >
              Cerrar sesión
            </Link>
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
          <Link
            :href="route('dashboard')"
            class="block ps-3 pe-4 py-2 text-base font-medium
                   text-gray-600 hover:bg-gray-50 hover:text-gray-800
                   dark:text-gray-300 dark:hover:bg-gray-800/70 dark:hover:text-gray-100"
          >
            Dashboard
          </Link>

          <Link
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
        </div>

        <div class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-800">
          <div class="px-4">
            <div class="text-base font-medium text-gray-800 dark:text-gray-100">
              <Link v-if="myPublicHref" :href="myPublicHref" class="hover:underline">
                {{ firstName }}
              </Link>
              <span v-else>{{ firstName }}</span>
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

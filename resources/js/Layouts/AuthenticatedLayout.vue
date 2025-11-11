<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ToggleThemeButton from '@/Components/ToggleThemeButton.vue'

const showingNavigationDropdown = ref(false)

/**
 * Wrappers seguros para Ziggy:
 * - Si route() no existe => devolvemos null/false (no rompe)
 * - Si faltan params => capturamos la excepción y devolvemos null
 */
const canRoute = () => typeof route === 'function'

const has = (name) => {
  if (!canRoute()) return false
  try { return !!(route().has && route().has(name)) } catch { return false }
}

const href = (name, params = undefined) => {
  if (!canRoute()) return null
  try { return params !== undefined ? route(name, params) : route(name) } catch { return null }
}

const current = (name) => {
  if (!canRoute()) return false
  try { return !!(route().current && route().current(name)) } catch { return false }
}
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <nav class="border-b border-gray-100 bg-white dark:bg-gray-900 dark:border-gray-800">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <!-- IZQUIERDA: logo + links -->
          <div class="flex items-center gap-6">
            <Link :href="href('dashboard') || '#'" class="font-semibold text-gray-900 dark:text-gray-100">
              FP Empresa
            </Link>

            <Link
              :href="href('dashboard') || '#'"
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:text-gray-900 hover:bg-gray-50
                     dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70"
              :class="{ 'bg-gray-100 dark:bg-gray-800': current('dashboard') }"
            >
              Dashboard
            </Link>

            <Link
              :href="href('vacancies.index') || '#'"
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:text-gray-900 hover:bg-gray-50
                     dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70"
              :class="{ 'bg-gray-100 dark:bg-gray-800': current('vacancies.index') }"
            >
              Vacantes
            </Link>

            <!-- Matchings (solo si existe la ruta) -->
            <Link
              v-if="has('matchings.index') && href('matchings.index')"
              :href="href('matchings.index')"
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:text-gray-900 hover:bg-gray-50
                     dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70"
              :class="{ 'bg-gray-100 dark:bg-gray-800': current('matchings.index') }"
            >
              Matchings
            </Link>

            <!-- Mi perfil (edición) => siempre visible; activo solo si hay studentId -->
            <template v-if="has('students.edit')">
              <Link
                v-if="href('students.edit', $page.props.auth?.studentId)"
                :href="href('students.edit', $page.props.auth.studentId)"
                class="text-sm rounded-lg px-3 py-2 font-medium
                       text-gray-600 hover:text-gray-900 hover:bg-gray-50
                       dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70"
                :class="{ 'bg-gray-100 dark:bg-gray-800': current('students.edit') }"
              >
                Mi perfil
              </Link>
              <span
                v-else
                class="text-sm rounded-lg px-3 py-2 font-medium text-gray-400 dark:text-gray-500 cursor-not-allowed"
                title="Creando tu perfil… recarga si persiste"
              >
                Mi perfil
              </span>
            </template>
          </div>

          <!-- DERECHA: tema + usuario -->
          <div class="hidden sm:flex items-center gap-3">
            <ToggleThemeButton class="me-1" />

            <!-- Nombre → enlace al perfil público si hay studentId -->
            <template v-if="has('students.public.show')">
              <Link
                v-if="href('students.public.show', $page.props.auth?.studentId)"
                :href="href('students.public.show', $page.props.auth.studentId)"
                class="text-sm text-gray-600 dark:text-gray-300 hover:underline"
              >
                {{ (($page.props.auth?.user?.name || 'Usuario').trim().split(' '))[0] }}
              </Link>
              <span v-else class="text-sm text-gray-600 dark:text-gray-300">
                {{ (($page.props.auth?.user?.name || 'Usuario').trim().split(' '))[0] }}
              </span>
            </template>

            <Link
              v-if="has('profile.edit')"
              :href="href('profile.edit') || '#'"
              class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
            >
              Cuenta
            </Link>

            <Link
              v-if="has('logout')"
              :href="href('logout') || '#'"
              method="post"
              as="button"
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:text-gray-900 hover:bg-gray-50
                     dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70"
            >
              Cerrar sesión
            </Link>
          </div>

          <!-- MÓVIL: hamburguesa -->
          <div class="-me-2 flex items-center sm:hidden">
            <button
              @click="showingNavigationDropdown = !showingNavigationDropdown"
              class="inline-flex items-center justify-center rounded-md p-2 text-gray-400
                     hover:bg-gray-100 hover:text-gray-500 focus:outline-none
                     dark:hover:bg-gray-800"
            >
              <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                      stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- MENÚ MÓVIL -->
      <div class="sm:hidden" :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }">
        <div class="px-4 pb-2 pt-3 flex items-center justify-between">
          <span class="text-sm text-gray-600 dark:text-gray-300">Tema</span>
          <ToggleThemeButton />
        </div>

        <div class="space-y-1 pb-3 pt-2">
          <Link :href="href('dashboard') || '#'"
                class="block ps-3 pe-4 py-2 text-base font-medium
                       text-gray-600 hover:text-gray-800 hover:bg-gray-50
                       dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70">
            Dashboard
          </Link>

          <Link :href="href('vacancies.index') || '#'"
                class="block ps-3 pe-4 py-2 text-base font-medium
                       text-gray-600 hover:text-gray-800 hover:bg-gray-50
                       dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70">
            Vacantes
          </Link>

          <Link
            v-if="has('matchings.index') && href('matchings.index')"
            :href="href('matchings.index')"
            class="block ps-3 pe-4 py-2 text-base font-medium
                   text-gray-600 hover:text-gray-800 hover:bg-gray-50
                   dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70">
            Matchings
          </Link>

          <!-- Mi perfil (móvil) -->
          <template v-if="has('students.edit')">
            <Link
              v-if="href('students.edit', $page.props.auth?.studentId)"
              :href="href('students.edit', $page.props.auth.studentId)"
              class="block ps-3 pe-4 py-2 text-base font-medium
                     text-gray-600 hover:text-gray-800 hover:bg-gray-50
                     dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70">
              Mi perfil
            </Link>
            <span
              v-else
              class="block ps-3 pe-4 py-2 text-base font-medium text-gray-400 dark:text-gray-500 cursor-not-allowed">
              Mi perfil
            </span>
          </template>
        </div>

        <div class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-800">
          <div class="px-4">
            <div class="text-base font-medium text-gray-800 dark:text-gray-100">
              <template v-if="has('students.public.show')">
                <Link
                  v-if="href('students.public.show', $page.props.auth?.studentId)"
                  :href="href('students.public.show', $page.props.auth.studentId)"
                  class="hover:underline"
                >
                  {{ (($page.props.auth?.user?.name || 'Usuario').trim().split(' '))[0] }}
                </Link>
                <span v-else>
                  {{ (($page.props.auth?.user?.name || 'Usuario').trim().split(' '))[0] }}
                </span>
              </template>
            </div>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
              {{ $page.props.auth?.user?.email || '' }}
            </div>
          </div>

          <div class="mt-3 space-y-1 px-4">
            <Link
              v-if="has('profile.edit')"
              :href="href('profile.edit') || '#'"
              class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
            >
              Cuenta
            </Link>
            <Link
              v-if="has('logout')"
              :href="href('logout') || '#'"
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

    <header v-if="$slots.header" class="bg-white shadow dark:bg-gray-900 dark:shadow-none">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <main>
      <slot />
    </main>
  </div>
</template>

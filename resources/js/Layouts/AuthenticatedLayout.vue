<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ToggleThemeButton from '@/Components/ToggleThemeButton.vue'

const showingNavigationDropdown = ref(false)
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <nav class="border-b border-gray-100 bg-white dark:bg-gray-900 dark:border-gray-800">
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
                     text-gray-600 hover:text-gray-900 hover:bg-gray-50
                     dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70"
              :class="{ 'bg-gray-100 dark:bg-gray-800': route().current('dashboard') }"
            >
              Dashboard
            </Link>

            <Link
              :href="route('vacancies.index')"
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:text-gray-900 hover:bg-gray-50
                     dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70"
              :class="{ 'bg-gray-100 dark:bg-gray-800': route().current('vacancies.index') }"
            >
              Vacantes
            </Link>

            <!-- Solo muestra Matchings si la ruta existe -->
            <Link
              v-if="route().has && route().has('matchings.index')"
              :href="route('matchings.index')"
              class="text-sm rounded-lg px-3 py-2 font-medium
                     text-gray-600 hover:text-gray-900 hover:bg-gray-50
                     dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70"
              :class="{ 'bg-gray-100 dark:bg-gray-800': route().current('matchings.index') }"
            >
              Matchings
            </Link>
          </div>

          <!-- DERECHA: tema + usuario -->
          <div class="hidden sm:flex items-center gap-3">
            <!-- 👇 Botón Modo Día/Noche -->
            <ToggleThemeButton class="me-1" />

            <span class="text-sm text-gray-600 dark:text-gray-300">
              {{ $page.props.auth?.user?.name || 'Usuario' }}
            </span>

            <!-- Solo enlaza al perfil si la ruta existe -->
            <Link
              v-if="route().has && route().has('profile.edit')"
              :href="route('profile.edit')"
              class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
            >
              Perfil
            </Link>

            <Link
              v-if="route().has && route().has('logout')"
              :href="route('logout')"
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
        <!-- Fila con el botón de Tema en móvil -->
        <div class="px-4 pb-2 pt-3 flex items-center justify-between">
          <span class="text-sm text-gray-600 dark:text-gray-300">Tema</span>
          <ToggleThemeButton />
        </div>

        <div class="space-y-1 pb-3 pt-2">
          <Link :href="route('dashboard')"
                class="block ps-3 pe-4 py-2 text-base font-medium
                       text-gray-600 hover:text-gray-800 hover:bg-gray-50
                       dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70">
            Dashboard
          </Link>
          <Link :href="route('vacancies.index')"
                class="block ps-3 pe-4 py-2 text-base font-medium
                       text-gray-600 hover:text-gray-800 hover:bg-gray-50
                       dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70">
            Vacantes
          </Link>

          <Link
            v-if="route().has && route().has('matchings.index')"
            :href="route('matchings.index')"
            class="block ps-3 pe-4 py-2 text-base font-medium
                   text-gray-600 hover:text-gray-800 hover:bg-gray-50
                   dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70">
            Matchings
          </Link>
        </div>

        <div class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-800">
          <div class="px-4">
            <div class="text-base font-medium text-gray-800 dark:text-gray-100">
              {{ $page.props.auth?.user?.name || 'Usuario' }}
            </div>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
              {{ $page.props.auth?.user?.email || '' }}
            </div>
          </div>

          <div class="mt-3 space-y-1 px-4">
            <Link
              v-if="route().has && route().has('profile.edit')"
              :href="route('profile.edit')"
              class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
            >
              Perfil
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

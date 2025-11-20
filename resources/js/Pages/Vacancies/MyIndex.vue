<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

/**
 * Aceptamos ambos formatos para no romper:
 *  - vacancies: paginator de Laravel (con .data y .links)
 *  - items:     array simple de vacantes
 */
const props = defineProps({
  vacancies: { type: Object, default: null }, // paginator
  items: { type: Array, default: () => [] },  // fallback
})

// Normalizamos a un array de filas siempre
const rows = computed(() => {
  if (props?.vacancies?.data && Array.isArray(props.vacancies.data)) return props.vacancies.data
  if (Array.isArray(props.items) && props.items.length) return props.items
  return []
})

// Normalizamos links de paginación (si vienen)
const links = computed(() => {
  if (props?.vacancies?.links && Array.isArray(props.vacancies.links)) return props.vacancies.links
  return []
})

// Formatear modalidad
const formatMode = (mode) => {
  if (!mode) return null
  if (mode === 'remoto') return 'Teletrabajo'
  if (mode === 'hibrido' || mode === 'híbrido') return 'Híbrido'
  if (mode === 'presencial') return 'Presencial'
  return mode
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Mis vacantes" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mis vacantes</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
              Gestiona tus ofertas de prácticas publicadas
            </p>
          </div>

          <Link
            v-if="route().has && route().has('vacancies.create')"
            :href="route('vacancies.create')"
            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-indigo-500 dark:to-purple-500"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Crear vacante
          </Link>
          <a
            v-else
            href="/vacantes/crear"
            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-indigo-500 dark:to-purple-500"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Crear vacante
          </a>
        </div>

        <!-- GRID -->
        <div v-if="rows.length" class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
          <div
            v-for="v in rows"
            :key="v.id"
            class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-gray-800 dark:bg-gray-900"
          >
            <!-- Efecto blur -->
            <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-indigo-100 opacity-0 blur-3xl transition-opacity duration-300 group-hover:opacity-50 dark:bg-indigo-900/30"></div>
            
            <div class="relative p-6">
              <div class="mb-3 flex items-start justify-between gap-3">
                <h3 class="flex-1 text-lg font-bold text-gray-900 dark:text-white">
                  {{ v.title || 'Sin título' }}
                </h3>
                <span class="shrink-0 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                  Publicada
                </span>
              </div>

              <p class="mb-4 line-clamp-2 text-sm text-gray-600 dark:text-gray-400">
                {{ v.description || 'Sin descripción' }}
              </p>

              <div class="mb-4 space-y-2 text-sm">
                <div v-if="v.cycle_required" class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                  <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                  <span class="font-medium">{{ v.cycle_required }}</span>
                </div>
                <div v-if="v.mode" class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                  <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  <span>{{ formatMode(v.mode) }}</span>
                </div>
                <div v-if="v.city || v.province" class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                  <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span>{{ v.city }}<span v-if="v.city && v.province"> • </span>{{ v.province }}</span>
                </div>
              </div>

              <div class="flex items-center gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <Link
                  :href="route('vacancies.show', v.id)"
                  class="group/link flex items-center gap-1.5 text-sm font-semibold text-indigo-600 transition-colors hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                  Ver detalles
                  <svg class="h-4 w-4 transition-transform group-hover/link:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                  </svg>
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- EMPTY -->
        <div v-else class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
          <div class="px-6 py-16 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
              <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">No tienes vacantes</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Aún no has publicado ninguna vacante. Crea la primera desde el botón "Crear vacante".
            </p>
          </div>
        </div>

        <!-- PAGINACIÓN -->
        <div v-if="links.length" class="mt-8 flex flex-wrap items-center justify-center gap-2">
          <Link
            v-for="link in links"
            :key="link.url ?? link.label"
            :href="link.url || '#'"
            v-html="link.label"
            class="rounded-lg border px-4 py-2 text-sm font-medium transition-all duration-200 hover:scale-105"
            :class="[
              link.active 
                ? 'border-indigo-600 bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md dark:from-indigo-500 dark:to-purple-500' 
                : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700',
              !link.url ? 'opacity-40 pointer-events-none' : ''
            ]"
          />
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

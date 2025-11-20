<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  vacantes: Object, // paginator de Laravel (data, links, meta…)
  filters:  Object, // { ciclo, modalidad, ubicacion }
})

const page  = usePage()
const flash = computed(() => page.props.flash || {})

/* ---- Rol actual (tolerante a distintos esquemas) ---- */
const rawRole = computed(() =>
  page.props.auth?.roleSlug ??
  page.props.auth?.role ??
  page.props.auth?.user?.role ??
  null
)
const norm = (v) => !v ? null : String(v).trim().toUpperCase()
const role = computed(() => norm(rawRole.value))
const isStudent = computed(() => role.value === 'STUDENT' || role.value === 'STUDENTs' || role.value === 'ALUMNO' || role.value === 'ALUMNOS' || role.value === 'STUDENT'.toUpperCase())
const canCreate = computed(() => {
  const r = role.value
  return r === 'COMPANY' || r === 'PROFESSOR' || r === 'ADMIN' || r === 'EMPRESA'
})

/* ---- Filtros ---- */
function filtrar(e) {
  e.preventDefault()
  const form = new FormData(e.target)
  router.get('/vacantes', Object.fromEntries(form), {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}
function limpiar() {
  router.get('/vacantes', {}, {
    preserveState: false,
    preserveScroll: true,
    replace: true,
  })
}

/* ---- Aplicar (solo alumnos) ---- */
function aplicar(id) {
  router.post(`/vacantes/${id}/aplicar`, {}, { preserveScroll: true })
}

/* ---- Helpers visuales ---- */
const formatMode = (m) => {
  const v = (m || '').toString().toLowerCase()
  if (v === 'remoto') return 'Teletrabajo'
  if (v === 'hibrido' || v === 'híbrido') return 'Híbrido'
  if (v === 'presencial') return 'Presencial'
  return 'Presencial'
}
const ubicacionDe = (v) => {
  // Soporta `ubicacion` (cadena) o city/province
  if (v.ubicacion) return v.ubicacion
  const city = v.city || ''
  const prov = v.province || ''
  if (city && prov) return `${city} — ${prov}`
  return city || prov || '—'
}
const cicloDe = (v) => v.ciclo_requerido || v.cycle_required || '—'
const modalidadDe = (v) => v.modalidad || v.mode || null
const createdDe = (v) => (v.created_at || '').slice(0, 10)

// score: admitimos varios nombres
const scoreOf = (v) => {
  const s = v.score ?? v.compatibility ?? v.match_score
  if (s === undefined || s === null) return null
  const n = Number(s)
  if (Number.isNaN(n)) return null
  return Math.round(n)
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Vacantes" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Vacantes Disponibles</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
              Explora oportunidades de prácticas que se ajusten a tu perfil
            </p>
          </div>

          <!-- Crear vacante (solo empresa / profesor / admin) -->
          <div v-if="canCreate">
            <Link
              v-if="route().has && route().has('vacancies.create')"
              :href="route('vacancies.create')"
              class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-indigo-500 dark:to-purple-500"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Crear vacante
            </Link>
            <a
              v-else
              href="/vacantes/crear"
              class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-indigo-500 dark:to-purple-500"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Crear vacante
            </a>
          </div>
        </div>

        <!-- Flash messages -->
        <div v-if="flash.success" class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-gradient-to-r from-green-50 to-emerald-50 p-4 shadow-sm dark:border-green-900/50 dark:from-green-900/30 dark:to-emerald-900/20">
          <svg class="h-5 w-5 shrink-0 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
          </svg>
          <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ flash.success }}</p>
        </div>
        <div v-if="flash.error" class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-gradient-to-r from-red-50 to-pink-50 p-4 shadow-sm dark:border-red-900/50 dark:from-red-900/30 dark:to-pink-900/20">
          <svg class="h-5 w-5 shrink-0 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ flash.error }}</p>
        </div>

        <!-- Filtros -->
        <form @submit="filtrar" class="mb-8 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
          <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
            <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
              <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
              </svg>
              Filtros de búsqueda
            </h3>
          </div>
          
          <div class="p-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
              <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">Ciclo Formativo</label>
                <input
                  name="ciclo"
                  class="w-full rounded-xl border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
                  :value="filters?.ciclo || ''"
                  placeholder="Ej: DAW, DAM, ASIR..."
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">Modalidad</label>
                <select
                  name="modalidad"
                  class="w-full rounded-xl border-gray-300 bg-white px-4 py-2.5 text-gray-900 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
                  :value="filters?.modalidad || ''"
                >
                  <option value="">Todas las modalidades</option>
                  <option value="presencial">Presencial</option>
                  <option value="híbrido">Híbrido</option>
                  <option value="remoto">Remoto</option>
                </select>
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-200">Ubicación</label>
                <input
                  name="ubicacion"
                  class="w-full rounded-xl border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
                  :value="filters?.ubicacion || ''"
                  placeholder="Ciudad, provincia..."
                />
              </div>

              <div class="flex items-end gap-3">
                <button
                  type="submit"
                  class="flex-1 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg dark:from-indigo-500 dark:to-purple-500"
                >
                  Filtrar
                </button>
                <button
                  type="button"
                  @click="limpiar"
                  class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                  Limpiar
                </button>
              </div>
            </div>
          </div>
        </form>

        <!-- Grid de vacantes -->
        <div v-if="vacantes?.data?.length" class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
          <div
            v-for="v in vacantes.data"
            :key="v.id"
            class="group relative overflow-hidden rounded-2xl border border-gray-100 bg-white p-6 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-gray-800 dark:bg-gray-900"
          >
            <!-- Efecto blur de fondo -->
            <div class="pointer-events-none absolute -right-8 -top-8 h-32 w-32 rounded-full bg-indigo-100 opacity-0 blur-3xl transition-opacity duration-300 group-hover:opacity-50 dark:bg-indigo-900/30"></div>
            
            <!-- Cabecera de la tarjeta -->
            <div class="relative mb-4">
              <div class="mb-3 flex items-start justify-between gap-3">
                <h3 class="flex-1 text-lg font-bold text-gray-900 dark:text-white">
                  {{ v.title }}
                </h3>

                <!-- Etiqueta de modalidad -->
                <span
                  v-if="modalidadDe(v)"
                  class="shrink-0 rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300"
                >
                  {{ formatMode(modalidadDe(v)) }}
                </span>
              </div>

              <!-- Datos básicos -->
              <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-center gap-2">
                  <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                  <span class="font-medium text-gray-700 dark:text-gray-300">{{ cicloDe(v) }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                  </svg>
                  <span class="text-gray-700 dark:text-gray-300">{{ ubicacionDe(v) }}</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500">
                  <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  Publicada: {{ createdDe(v) }}
                </div>
              </div>
            </div>

            <!-- Compatibilidad (si existe) -->
            <div
              v-if="scoreOf(v) !== null"
              class="relative mb-4 overflow-hidden rounded-xl bg-gradient-to-r from-emerald-50 to-green-50 p-3 dark:from-emerald-950/30 dark:to-green-950/20"
            >
              <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-400">
                  Compatibilidad
                </span>
                <span class="flex items-center gap-1.5 rounded-full bg-gradient-to-r from-emerald-600 to-green-600 px-3 py-1 text-sm font-bold text-white shadow-sm">
                  <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                  {{ scoreOf(v) }}%
                </span>
              </div>
            </div>

            <!-- Acciones -->
            <div class="relative flex flex-wrap items-center gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
              <!-- Aplicar (solo alumnos) -->
              <button
                v-if="isStudent"
                @click="aplicar(v.id)"
                class="flex-1 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg dark:from-indigo-500 dark:to-purple-500"
              >
                Aplicar ahora
              </button>

              <!-- Ver más -->
              <Link
                :href="route().has && route().has('vacancies.show') ? route('vacancies.show', v.id) : `/vacantes/${v.id}`"
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

        <!-- Empty state -->
        <div v-else class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
          <div class="px-6 py-16 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
              <svg class="h-8 w-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">No hay vacantes disponibles</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              No se encontraron vacantes que coincidan con los filtros aplicados. Intenta ajustar los criterios de búsqueda.
            </p>
          </div>
        </div>

        <!-- Paginación -->
        <div v-if="vacantes?.links?.length" class="mt-8 flex flex-wrap items-center justify-center gap-2">
          <template v-for="(link, i) in vacantes.links" :key="i">
            <span
              v-if="!link.url"
              class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-2 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500"
              v-html="link.label"
            />
            <Link
              v-else
              :href="link.url"
              preserve-state
              preserve-scroll
              class="rounded-lg border px-4 py-2 text-sm font-medium transition-all duration-200 hover:scale-105"
              :class="link.active 
                ? 'border-indigo-600 bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md dark:from-indigo-500 dark:to-purple-500' 
                : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'"
              v-html="link.label"
            />
          </template>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

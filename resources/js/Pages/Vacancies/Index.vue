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
  if (v === 'remote' || v === 'remoto') return 'Remoto'
  if (v === 'hybrid' || v === 'híbrido' || v === 'hibrido') return 'Híbrido'
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

    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Vacantes</h2>

        <!-- Crear vacante (solo empresa / profesor / admin) -->
        <div v-if="canCreate" class="flex items-center gap-2">
          <Link
            v-if="route().has && route().has('vacancies.create')"
            :href="route('vacancies.create')"
            class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium
                   text-white bg-indigo-600 hover:bg-indigo-700
                   focus:outline-none focus:ring-2 focus:ring-indigo-500/50
                   dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-400/40"
          >
            Crear vacante
          </Link>
          <a
            v-else
            href="/vacantes/crear"
            class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium
                   text-white bg-indigo-600 hover:bg-indigo-700
                   focus:outline-none focus:ring-2 focus:ring-indigo-500/50
                   dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-400/40"
          >
            Crear vacante
          </a>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

        <!-- Flash messages -->
        <div v-if="flash.success" class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-900/30 dark:text-green-300">
          {{ flash.success }}
        </div>
        <div v-if="flash.error" class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-900/50 dark:bg-red-900/30 dark:text-red-300">
          {{ flash.error }}
        </div>

        <!-- Filtros -->
        <form @submit="filtrar" class="mb-6 rounded-2xl border border-gray-100 bg-white p-4 shadow dark:border-gray-800 dark:bg-gray-900">
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Ciclo</label>
              <input
                name="ciclo"
                class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 placeholder-gray-400
                       focus:border-indigo-500 focus:ring-indigo-500
                       dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                :value="filters?.ciclo || ''"
                placeholder="DAW, DAM…"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Modalidad</label>
              <select
                name="modalidad"
                class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900
                       focus:border-indigo-500 focus:ring-indigo-500
                       dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                :value="filters?.modalidad || ''"
              >
                <option value="">Cualquiera</option>
                <option value="presencial">Presencial</option>
                <option value="híbrido">Híbrido</option>
                <option value="remoto">Remoto</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Ubicación</label>
              <input
                name="ubicacion"
                class="mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 placeholder-gray-400
                       focus:border-indigo-500 focus:ring-indigo-500
                       dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                :value="filters?.ubicacion || ''"
                placeholder="Madrid, remoto…"
              />
            </div>

            <div class="flex items-end gap-2">
              <button
                type="submit"
                class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium
                       text-white bg-indigo-600 hover:bg-indigo-700
                       focus:outline-none focus:ring-2 focus:ring-indigo-500/50
                       dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-400/40"
              >
                Filtrar
              </button>
              <button
                type="button"
                @click="limpiar"
                class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium
                       text-gray-700 bg-gray-100 hover:bg-gray-200
                       focus:outline-none focus:ring-2 focus:ring-gray-300/60
                       dark:text-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-600/40"
              >
                Limpiar
              </button>
            </div>
          </div>
        </form>

        <!-- Grid de vacantes -->
        <div v-if="vacantes?.data?.length" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
          <div
            v-for="v in vacantes.data"
            :key="v.id"
            class="rounded-2xl border border-gray-100 bg-white p-5 shadow transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900"
          >
            <!-- Cabecera de la tarjeta -->
            <div class="flex items-start justify-between gap-3">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                {{ v.title }}
              </h3>

              <!-- Etiqueta de modalidad (solo modalidad aquí, SIN compatibilidad) -->
              <div class="flex shrink-0 items-center gap-1">
                <span
                  v-if="modalidadDe(v)"
                  class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300"
                >
                  {{ formatMode(modalidadDe(v)) }}
                </span>
              </div>
            </div>

            <!-- Datos básicos -->
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              <div class="flex items-center gap-2">
                <span class="inline-flex h-2 w-2 rounded-full bg-gray-400"></span>
                <span class="font-medium">Ciclo:</span>
                <span>{{ cicloDe(v) }}</span>
              </div>
              <div class="mt-1 flex items-center gap-2">
                <span class="inline-flex h-2 w-2 rounded-full bg-gray-400"></span>
                <span class="font-medium">Ubicación:</span>
                <span>{{ ubicacionDe(v) }}</span>
              </div>
            </div>

            <!-- Parte inferior: fecha + botones izquierda / compatibilidad abajo derecha -->
            <div class="mt-4 flex items-end justify-between">
              <div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                  Publicada: {{ createdDe(v) }}
                </div>

                <div class="mt-2 flex flex-wrap items-center gap-2">
                  <!-- Aplicar (solo alumnos) -->
                  <button
                    v-if="isStudent"
                    @click="aplicar(v.id)"
                    class="inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium
                           text-white bg-indigo-600 hover:bg-indigo-700
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/50
                           dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-400/40"
                  >
                    Aplicar
                  </button>

                  <!-- Ver más -->
                  <Link
                    :href="route().has && route().has('vacancies.show') ? route('vacancies.show', v.id) : `/vacantes/${v.id}`"
                    class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
                  >
                    Ver más
                  </Link>
                </div>
              </div>

              <!-- Compatibilidad abajo a la derecha -->
              <div
                v-if="scoreOf(v) !== null"
                class="flex flex-col items-end text-right"
              >
                <span class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">
                  Compatibilidad
                </span>
                <span
                  class="mt-1 inline-flex items-center rounded-full bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white"
                  :title="'Compatibilidad con esta vacante'"
                >
                  {{ scoreOf(v) }}%
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div v-else class="rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-600 shadow dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
          No hay vacantes que coincidan con el filtro.
        </div>

        <!-- Paginación -->
        <div v-if="vacantes?.links?.length" class="mt-6 flex flex-wrap items-center gap-2">
          <template v-for="(link, i) in vacantes.links" :key="i">
            <span
              v-if="!link.url"
              class="rounded-lg px-3 py-2 text-sm text-gray-400 dark:text-gray-500"
              v-html="link.label"
            />
            <Link
              v-else
              :href="link.url"
              preserve-state
              preserve-scroll
              class="rounded-lg px-3 py-2 text-sm
                     text-gray-600 hover:text-gray-900 hover:bg-gray-50
                     dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70"
              :class="{ 'bg-gray-100 dark:bg-gray-800': link.active }"
              v-html="link.label"
            />
          </template>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

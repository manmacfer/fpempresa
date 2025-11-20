<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  vacancy:  { type: Object, required: true },
  canMatch: { type: Boolean, default: false },
  hasApplication: { type: Boolean, default: false },
  applicationId: { type: Number, default: null },
})

const page = usePage()
const auth = computed(() => page.props.auth || {})

// El rol viene como string plano desde el backend
const role = computed(() => auth.value.role ?? null)
const isCompany = computed(() => role.value === 'company')

// Propietario (empresa)
const userId = computed(() => auth.value.id ?? null)
const isOwner = computed(() => {
  if (!userId.value) return false
  return userId.value === props.vacancy?.company?.user_id
    || userId.value === props.vacancy?.user_id
})

// Datos cabecera
const companyName = computed(() =>
  props.vacancy.company?.trade_name ||
  props.vacancy.company?.legal_name ||
  'Empresa'
)

// URL a “Ver Match” (vista de empresa)
const matchUrl = computed(() => {
  try {
    if (route().has && route().has('matchings.index')) {
      return route('matchings.index', { vacancy_id: props.vacancy.id })
    }
  } catch (_) {}
  return null
})

// Botón Match (alumno)
const matchForm = useForm({})
const doMatch = () => {
  console.log('Intentando hacer Match con vacante:', props.vacancy.id)
  console.log('Route:', route('applications.store', props.vacancy.id))
  
  matchForm.post(route('applications.store', props.vacancy.id), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Match realizado con éxito')
    },
    onError: (errors) => {
      console.error('Error al hacer Match:', errors)
    }
  })
}

// Cancelar Match (alumno)
const cancelForm = useForm({})
const cancelMatch = () => {
  if (!confirm('¿Cancelar tu candidatura a esta vacante?')) return
  
  cancelForm.delete(route('applications.destroy', props.applicationId), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Candidatura cancelada con éxito')
    },
    onError: (errors) => {
      console.error('Error al cancelar:', errors)
    }
  })
}

// Delete vacancy (empresa)
const deleteForm = useForm({})
function deleteVacancy() {
  if (!confirm('¿Eliminar esta vacante? Esta acción no se puede deshacer.')) return
  deleteForm.delete(route('vacancies.destroy', props.vacancy.id), {
    preserveScroll: true,
    onSuccess: () => {
      try { router.visit(route('vacancies.my')) } catch (_) {}
    }
  })
}

// Formatear modalidad
const formatMode = (mode) => {
  if (!mode) return null
  if (mode === 'remoto') return 'Teletrabajo'
  if (mode === 'hibrido' || mode === 'híbrido') return 'Híbrido'
  if (mode === 'presencial') return 'Presencial'
  return mode
}

// Badges
const badge = (text, tone = 'gray') => {
  const tones = {
    gray:   'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    indigo: 'bg-indigo-50 text-indigo-700 dark:bg-indigo-400/20 dark:text-indigo-200',
    green:  'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
  }
  return `inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ${tones[tone] || tones.gray}`
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="props.vacancy?.title || 'Vacante'" />
    
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        
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

        <!-- Header Card con gradiente -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900">
          <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 dark:from-indigo-700 dark:to-purple-700">
            <div class="mb-4 flex items-center gap-2 text-sm text-indigo-100">
              <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
              </svg>
              <span>Empresa: <b class="text-white">{{ companyName }}</b></span>
            </div>
            <h1 class="mb-3 text-3xl font-bold text-white md:text-4xl">
              {{ props.vacancy.title }}
            </h1>
            
            <!-- Badges informativos -->
            <div class="flex flex-wrap gap-2">
              <span v-if="props.vacancy.cycle_required" class="inline-flex items-center gap-1.5 rounded-full border border-white/30 bg-white/20 px-3 py-1.5 text-sm font-semibold text-white backdrop-blur-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                {{ props.vacancy.cycle_required }}
              </span>
              <span v-if="props.vacancy.mode" class="inline-flex items-center gap-1.5 rounded-full border border-white/30 bg-white/20 px-3 py-1.5 text-sm font-semibold text-white backdrop-blur-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                {{ formatMode(props.vacancy.mode) }}
              </span>
              <span v-if="props.vacancy.city || props.vacancy.province" class="inline-flex items-center gap-1.5 rounded-full border border-white/30 bg-white/20 px-3 py-1.5 text-sm font-semibold text-white backdrop-blur-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ props.vacancy.city }}<span v-if="props.vacancy.city && props.vacancy.province"> • </span>{{ props.vacancy.province }}
              </span>
              <span v-if="props.vacancy.hours_per_week" class="inline-flex items-center gap-1.5 rounded-full border border-white/30 bg-white/20 px-3 py-1.5 text-sm font-semibold text-white backdrop-blur-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ props.vacancy.hours_per_week }} h/semana
              </span>
              <span v-if="props.vacancy.duration_weeks" class="inline-flex items-center gap-1.5 rounded-full border border-white/30 bg-white/20 px-3 py-1.5 text-sm font-semibold text-white backdrop-blur-sm">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ props.vacancy.duration_weeks }} semanas
              </span>
              <span v-if="props.vacancy.paid" class="inline-flex items-center gap-1.5 rounded-full border border-emerald-300/50 bg-emerald-400/30 px-3 py-1.5 text-sm font-semibold text-white backdrop-blur-sm">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Remunerada
              </span>
            </div>
          </div>

          <!-- Botones de acción -->
          <div class="border-t border-gray-100 bg-gray-50 p-6 dark:border-gray-800 dark:bg-gray-800/50">
            <div class="flex flex-wrap items-center gap-3">
              <!-- Empresa: Ver Match -->
              <Link
                v-if="isCompany && matchUrl"
                :href="matchUrl"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg dark:from-indigo-500 dark:to-purple-500"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Ver Match
              </Link>

              <!-- Alumno: Match -->
              <button
                v-if="role === 'student' && canMatch"
                @click="doMatch"
                :disabled="matchForm.processing"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg disabled:opacity-60 dark:from-indigo-500 dark:to-purple-500"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                Match
              </button>

              <!-- Alumno: Cancelar Match -->
              <button
                v-if="role === 'student' && hasApplication"
                @click="cancelMatch"
                :disabled="cancelForm.processing"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-orange-600 to-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg disabled:opacity-60"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar Match
              </button>

              <!-- Propietario: Editar -->
              <Link
                v-if="isOwner"
                :href="route('vacancies.edit', props.vacancy.id)"
                class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
              </Link>

              <!-- Propietario: Eliminar -->
              <button
                v-if="isOwner && route().has && route().has('vacancies.destroy')"
                @click="deleteVacancy"
                :disabled="deleteForm.processing"
                class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg disabled:opacity-60"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Eliminar
              </button>

              <!-- Volver -->
              <Link
                :href="route('vacancies.index')"
                class="ml-auto inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
              </Link>
            </div>
          </div>
        </div>

        <!-- Contenido -->
        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Descripción -->
        <section class="md:col-span-2 rounded-2xl bg-white p-6 shadow dark:bg-gray-800">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Descripción</h2>
          <div class="mt-3 text-gray-700 dark:text-gray-300 whitespace-pre-line">
            <template v-if="props.vacancy.description && props.vacancy.description.trim()">
              {{ props.vacancy.description }}
            </template>
            <em v-else class="text-gray-500 dark:text-gray-400">Sin descripción.</em>
          </div>
        </section>

        <!-- Lateral -->
        <aside class="rounded-2xl bg-white p-6 shadow dark:bg-gray-800">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detalles</h3>
          <dl class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-300">
            <div v-if="props.vacancy.salary_month && props.vacancy.paid" class="flex justify-between">
              <dt>Salario aprox.</dt>
              <dd><b>{{ new Intl.NumberFormat('es-ES').format(props.vacancy.salary_month) }} € / mes</b></dd>
            </div>
            <div v-if="props.vacancy.published_at" class="flex justify-between">
              <dt>Publicada</dt>
              <dd>{{ new Date(props.vacancy.published_at).toLocaleString('es-ES') }}</dd>
            </div>
            
            <!-- Horario -->
            <div v-if="props.vacancy.availability_slot" class="flex justify-between">
              <dt>Franja horaria</dt>
              <dd>
                <b>
                  {{ props.vacancy.availability_slot === 'manana' ? 'Mañana' : props.vacancy.availability_slot === 'tarde' ? 'Tarde' : 'Completa' }}
                </b>
              </dd>
            </div>
          </dl>

          <!-- Idiomas requeridos -->
          <div v-if="props.vacancy.required_languages?.length" class="mt-5">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Idiomas requeridos</h4>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="l in props.vacancy.required_languages"
                :key="l.id"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700 dark:bg-gray-900 dark:text-gray-300"
              >
                {{ l.name }}
                <span v-if="l.pivot?.min_level">({{ l.pivot.min_level }})</span>
              </span>
            </div>
          </div>

          <!-- Tecnologías -->
          <div v-if="props.vacancy.tech_stack?.length" class="mt-5">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Tecnologías</h4>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="t in props.vacancy.tech_stack"
                :key="t"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700 dark:bg-gray-900 dark:text-gray-300"
              >
                {{ t }}
              </span>
            </div>
          </div>

          <!-- Soft skills -->
          <div v-if="props.vacancy.soft_skills?.length" class="mt-5">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Soft skills</h4>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="s in props.vacancy.soft_skills"
                :key="s"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700 dark:bg-gray-900 dark:text-gray-300"
              >
                {{ s }}
              </span>
            </div>
          </div>
        </aside>
      </div>
    </div>
    </div>
  </AuthenticatedLayout>
</template>
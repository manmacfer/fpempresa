<template>
  <AuthenticatedLayout>
    <Head title="Mis Candidaturas" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Flash messages -->
        <div v-if="page.props.flash?.success" class="mb-6">
          <div class="rounded-xl bg-emerald-50 p-4 text-emerald-800 shadow-sm dark:bg-emerald-900/30 dark:text-emerald-200">
            ✓ {{ page.props.flash.success }}
          </div>
        </div>
        <div v-if="page.props.flash?.error" class="mb-6">
          <div class="rounded-xl bg-red-50 p-4 text-red-800 shadow-sm dark:bg-red-900/30 dark:text-red-200">
            ✕ {{ page.props.flash.error }}
          </div>
        </div>

        <!-- Header con gradiente -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-100 bg-gradient-to-br from-indigo-600 to-purple-600 p-8 shadow-xl dark:border-gray-800 dark:from-indigo-700 dark:to-purple-700">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-white">Mis Candidaturas</h1>
              <p class="mt-2 text-indigo-100">Gestiona tus aplicaciones y matches con empresas</p>
            </div>
            <Link
              :href="route('vacancies.index')"
              class="inline-flex items-center gap-2 rounded-xl border-2 border-white/30 bg-white/20 px-5 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:bg-white/30"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              Ver vacantes
            </Link>
          </div>
        </div>

        <!-- Empty state -->
        <div v-if="applications.length === 0" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
          <div class="flex flex-col items-center justify-center py-12 px-6">
            <svg class="h-16 w-16 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">No tienes candidaturas</h3>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">Explora las vacantes disponibles y aplica a las que te interesen</p>
            <Link
              :href="route('vacancies.index')"
              class="mt-6 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl dark:from-indigo-700 dark:to-purple-700"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
              Explorar vacantes
            </Link>
          </div>
        </div>

        <!-- Lista de candidaturas -->
        <ul v-else class="grid gap-6">
          <li v-for="m in applications" :key="m.id" class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    {{ m.vacancy?.title ?? m.title ?? 'Vacante' }}
                  </h2>
                  <p class="mt-1 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="font-medium">{{ companyName(m) }}</span>
                  </p>
                </div>

                <div class="flex flex-col items-end gap-2">
                  <span :class="statusBadgeClass(m.status)">
                    {{ statusLabel(m.status) }}
                  </span>
                  <span class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ formatDate(m.created_at ?? m.started_at) }}
                  </span>
                </div>
              </div>
            </div>

            <div class="p-6">
              <p v-if="m.vacancy?.description" class="text-sm leading-relaxed text-gray-700 dark:text-gray-300 line-clamp-3">
                {{ m.vacancy.description }}
              </p>
            </div>

            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/30">
              <div class="flex flex-wrap items-center gap-3">
                <Link
                  v-if="m.vacancy || m.vacancy_id"
                  :href="route('vacancies.show', m.vacancy?.id ?? m.vacancy_id)"
                  class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg disabled:opacity-60 dark:from-indigo-700 dark:to-purple-700"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  Ver vacante
                </Link>

                <button
                  v-if="m.status === 'PENDING' || m.status === 'EN_PROCESO' || m.status === 'ENVIADA' || m.status === 'enviada' || m.status === 'APPLICANT' || m.status === 'vista' || m.status === 'entrevista'"
                  @click.prevent="cancelApplication(m.id)"
                  :disabled="deleteForm.processing"
                  class="inline-flex items-center gap-2 rounded-xl border-2 border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-300 hover:scale-105 hover:border-red-300 hover:bg-red-50 hover:text-red-700 disabled:opacity-60 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  Cancelar candidatura
                </button>

                <span v-if="m.status === 'ACTIVO' || m.status === 'MATCH' || m.status === 'MATCHED'"
                  class="inline-flex items-center gap-2 rounded-xl bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  MATCH
                </span>

                <span v-if="m.status === 'DESCARTADO' || m.status === 'REJECTED'"
                  class="inline-flex items-center gap-2 rounded-xl bg-red-100 px-4 py-2 text-sm font-semibold text-red-800 dark:bg-red-900/30 dark:text-red-200"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  Descartado
                </span>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, usePage, router, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()

// adapta a lo que envíe el backend: 'matchings' o 'applications'
const applications = computed(() => page.props.matchings ?? page.props.applications ?? [])

// helpers de presentación
const companyName = (m) => {
  return m.vacancy?.company?.trade_name ?? m.vacancy?.company?.legal_name ?? m.company?.trade_name ?? 'Empresa'
}

const statusLabel = (s) => {
  if (!s) return 'En proceso'
  const t = String(s).toUpperCase()
  if (['ACTIVO','MATCH','MATCHED'].includes(t)) return 'MATCH'
  if (['CERRADO'].includes(t)) return 'Cerrado'
  if (['DESCARTADO','REJECTED','RECHAZADA'].includes(t)) return 'Descartado'
  if (['EN_PROCESO','PENDING','ENVIADA','APPLICANT','VISTA','ENTREVISTA'].includes(t)) return 'En proceso'
  if (['ACEPTADA'].includes(t)) return 'Aceptada'
  return s
}

const statusBadgeClass = (s) => {
  const t = String(s || '').toUpperCase()
  if (['ACTIVO','MATCH','MATCHED','ACEPTADA'].includes(t)) {
    return 'inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-200'
  }
  if (['DESCARTADO','REJECTED','RECHAZADA'].includes(t)) {
    return 'inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-200'
  }
  return 'inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-900 dark:text-gray-300'
}

const formatDate = (d) => {
  if (!d) return ''
  try { return new Date(d).toLocaleDateString('es-ES') } catch { return d }
}

// Usar Inertia para cancelar candidatura
const deleteForm = useForm({})
const cancelApplication = (applicationId) => {
  if (!confirm('¿Cancelar esta candidatura?')) return
  
  deleteForm.delete(route('applications.destroy', applicationId), {
    preserveScroll: true,
    onSuccess: () => {
      console.log('Candidatura cancelada')
    },
    onError: (errors) => {
      console.error('Error al cancelar:', errors)
      alert('Error al cancelar la candidatura')
    }
  })
}
</script>
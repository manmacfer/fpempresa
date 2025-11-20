<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  items: { type: Array, default: () => [] } // [{id,title,company,city,province,mode,score}]
})

const formatMode = (m) => {
  if (!m) return null
  if (m === 'remoto') return 'Teletrabajo'
  if (m === 'hibrido' || m === 'híbrido') return 'Híbrido'
  if (m === 'presencial') return 'Presencial'
  return m
}

const formatAvailabilitySlot = (slot) => {
  if (!slot) return null
  if (slot === 'manana' || slot === 'mañana') return 'Mañana'
  if (slot === 'tarde') return 'Tarde'
  if (slot === 'completa') return 'Completa'
  return slot
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Vacantes compatibles" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Header con gradiente -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-100 bg-gradient-to-br from-indigo-600 to-purple-600 p-8 shadow-xl dark:border-gray-800 dark:from-indigo-700 dark:to-purple-700">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-white">Vacantes Compatibles</h1>
              <p class="mt-2 text-indigo-100">Vacantes con ≥50% de compatibilidad con tu perfil</p>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-white/20 px-5 py-3 backdrop-blur-sm">
              <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span class="text-2xl font-bold text-white">{{ props.items.length }}</span>
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div v-if="!props.items.length" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
          <div class="flex flex-col items-center justify-center py-12 px-6">
            <svg class="h-16 w-16 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">No hay vacantes compatibles</h3>
            <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">Actualiza tu perfil para mejorar tu compatibilidad con las vacantes disponibles</p>
          </div>
        </div>

        <!-- Grid de vacantes -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
          <div v-for="v in props.items" :key="v.id" class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ v.title }}</h3>
                  <p class="mt-1 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ v.company?.trade_name || v.company?.legal_name || v.company || 'Empresa' }}
                  </p>
                </div>
                <div class="ml-3 flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 shadow-lg">
                  <span class="text-xl font-bold text-white">{{ v.score }}%</span>
                </div>
              </div>
            </div>

            <div class="p-6">
              <div class="space-y-3">
                <!-- Ciclo requerido -->
                <div v-if="v.cycle_required" class="flex items-center gap-2">
                  <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                  </svg>
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ciclo:</span>
                  <span class="rounded-full bg-indigo-100 px-3 py-0.5 text-xs font-semibold text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200">
                    {{ v.cycle_required }}
                  </span>
                </div>

                <!-- Franja horaria -->
                <div v-if="v.availability_slot" class="flex items-center gap-2">
                  <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Jornada:</span>
                  <span class="rounded-full bg-gray-100 px-3 py-0.5 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ formatAvailabilitySlot(v.availability_slot) }}
                  </span>
                </div>

                <!-- Ubicación y modalidad -->
                <div class="flex flex-wrap gap-2">
                  <div v-if="v.city || v.province" class="flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ v.city }}<span v-if="v.city && v.province">, </span>{{ v.province }}
                  </div>
                  <div v-if="v.mode" class="flex items-center gap-1.5 rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-200">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    {{ formatMode(v.mode) }}
                  </div>
                </div>
              </div>
            </div>

            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/30">
              <Link 
                :href="route('vacancies.show', v.id)"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-300 hover:scale-105 hover:shadow-lg dark:from-indigo-700 dark:to-purple-700"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Ver vacante
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

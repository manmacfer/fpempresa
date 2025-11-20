<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  matchings: Array
})
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Zona de Seguimiento" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
            Zona de Seguimiento
          </h1>
          <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            Gestiona las prácticas validadas y mantén la comunicación con todos los participantes
          </p>
        </div>

        <!-- Info box -->
        <div class="mb-6 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-xl p-4">
          <div class="flex items-start gap-3">
            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div class="text-sm text-indigo-800 dark:text-indigo-200">
              <p class="font-medium mb-1">Zona activa de prácticas</p>
              <p>Aquí aparecen todos los matchings que han sido validados por el centro. Puedes ver documentación, comunicarte con los participantes y hacer seguimiento del progreso.</p>
            </div>
          </div>
        </div>

        <!-- Sin matchings -->
        <div v-if="matchings.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-12 text-center">
          <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
          </svg>
          <h3 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">
            No hay prácticas en seguimiento
          </h3>
          <p class="mt-2 text-gray-600 dark:text-gray-400">
            Cuando un matching sea validado por el centro, aparecerá aquí para su seguimiento.
          </p>
        </div>

        <!-- Lista de matchings validados -->
        <div v-else class="space-y-4">
          <div
            v-for="matching in matchings"
            :key="matching.id"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden hover:shadow-xl transition-shadow duration-200"
          >
            <div class="p-6">
              <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                      {{ matching.vacancy?.title || 'Sin título' }}
                    </h3>
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-200">
                      <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                      </svg>
                      Validado
                    </span>
                  </div>
                  
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Empresa: <span class="font-medium text-gray-900 dark:text-white">{{ matching.company?.trade_name || matching.company?.legal_name }}</span>
                  </p>
                </div>

                <!-- Indicador de mensajes nuevos (placeholder para futuro) -->
                <div class="flex-shrink-0">
                  <div v-if="matching.conversation" class="relative">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Información del matching -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <!-- Alumno -->
                <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                  <img
                    v-if="matching.student?.avatar_path"
                    :src="`/storage/${matching.student.avatar_path}`"
                    :alt="matching.student.first_name"
                    class="h-10 w-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800"
                  />
                  <div
                    v-else
                    class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center ring-2 ring-white dark:ring-gray-800"
                  >
                    <span class="text-sm font-bold text-white">
                      {{ (matching.student?.first_name || '?')[0].toUpperCase() }}
                    </span>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Alumno</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                      {{ matching.student?.first_name }} {{ matching.student?.last_name }}
                    </p>
                  </div>
                </div>

                <!-- Empresa -->
                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Empresa</p>
                  <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ matching.company?.trade_name || matching.company?.legal_name }}
                  </p>
                </div>

                <!-- Ciclo -->
                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                  <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Ciclo</p>
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ matching.student?.cycle || 'Sin especificar' }}
                  </p>
                </div>
              </div>

              <!-- Acciones -->
              <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <Link
                  :href="route('seguimiento.show', matching.id)"
                  class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Ver Seguimiento
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

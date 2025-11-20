<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  matching: Object
})

const showValidateModal = ref(false)

const form = useForm({
  comentarios: ''
})

const validateMatching = () => {
  form.post(route('teacher.matchings.validate', props.matching.id), {
    onSuccess: () => {
      showValidateModal.value = false
      form.reset()
    }
  })
}

// Form para mensajes
const messageForm = useForm({
  content: ''
})

const sendMessage = () => {
  if (!messageForm.content.trim() || !props.matching.conversation) return
  
  messageForm.post(route('conversations.messages.store', props.matching.conversation.id), {
    onSuccess: () => {
      messageForm.reset()
    },
    preserveScroll: true
  })
}

// Verificar si hay conversación
const hasConversation = computed(() => {
  return props.matching.conversation && props.matching.conversation.id
})
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Matching: ${matching.vacancy?.title}`" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
          <Link
            :href="route('teacher.matchings.index')"
            class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 mb-4"
          >
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Volver a Matchings
          </Link>

          <div class="flex items-start justify-between gap-4">
            <div>
              <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                {{ matching.vacancy?.title }}
              </h1>
              <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                Revisión y validación del matching
              </p>
            </div>

            <div v-if="matching.validado_centro" class="flex-shrink-0">
              <span class="inline-flex items-center gap-2 rounded-full bg-green-100 px-4 py-2 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-200">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Validado por el centro
              </span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Columna principal -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Información del Alumno -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Alumno
              </h2>

              <div class="flex items-start gap-4">
                <img
                  v-if="matching.student?.avatar_path"
                  :src="`/storage/${matching.student.avatar_path}`"
                  :alt="matching.student.first_name"
                  class="h-20 w-20 rounded-xl object-cover ring-2 ring-white dark:ring-gray-800"
                />
                <div
                  v-else
                  class="h-20 w-20 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center ring-2 ring-white dark:ring-gray-800"
                >
                  <span class="text-2xl font-bold text-white">
                    {{ (matching.student?.first_name || '?')[0].toUpperCase() }}
                  </span>
                </div>

                <div class="flex-1">
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ matching.student?.first_name }} {{ matching.student?.last_name }}
                  </h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    {{ matching.student?.cycle }}
                  </p>
                  
                  <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                    <div v-if="matching.student?.phone">
                      <p class="text-gray-500 dark:text-gray-400">Teléfono</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ matching.student.phone }}</p>
                    </div>
                    <div v-if="matching.student?.user?.email">
                      <p class="text-gray-500 dark:text-gray-400">Email</p>
                      <p class="font-medium text-gray-900 dark:text-white">{{ matching.student.user.email }}</p>
                    </div>
                    <div v-if="matching.student?.city">
                      <p class="text-gray-500 dark:text-gray-400">Ubicación</p>
                      <p class="font-medium text-gray-900 dark:text-white">
                        {{ matching.student.city }}<span v-if="matching.student.province">, {{ matching.student.province }}</span>
                      </p>
                    </div>
                  </div>

                  <div class="mt-4">
                    <Link
                      :href="route('students.public.show', matching.student.id)"
                      class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                    >
                      Ver perfil completo
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Información de la Empresa -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Empresa
              </h2>

              <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                  {{ matching.company?.trade_name || matching.company?.legal_name }}
                </h3>
                
                <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                  <div v-if="matching.company?.phone">
                    <p class="text-gray-500 dark:text-gray-400">Teléfono</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ matching.company.phone }}</p>
                  </div>
                  <div v-if="matching.company?.user?.email">
                    <p class="text-gray-500 dark:text-gray-400">Email</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ matching.company.user.email }}</p>
                  </div>
                  <div v-if="matching.company?.city">
                    <p class="text-gray-500 dark:text-gray-400">Ubicación</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                      {{ matching.company.city }}<span v-if="matching.company.province">, {{ matching.company.province }}</span>
                    </p>
                  </div>
                </div>

                <div class="mt-4">
                  <Link
                    :href="route('companies.public.show', matching.company.id)"
                    class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                  >
                    Ver perfil de empresa
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </Link>
                </div>
              </div>
            </div>

            <!-- Descripción de la Vacante -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                Detalles de la Vacante
              </h2>

              <div v-if="matching.vacancy?.description" class="prose dark:prose-invert max-w-none text-sm text-gray-700 dark:text-gray-300 mb-4">
                {{ matching.vacancy.description }}
              </div>

              <div class="grid grid-cols-2 gap-3 text-sm">
                <div v-if="matching.vacancy?.city">
                  <p class="text-gray-500 dark:text-gray-400">Ubicación</p>
                  <p class="font-medium text-gray-900 dark:text-white">
                    {{ matching.vacancy.city }}<span v-if="matching.vacancy.province">, {{ matching.vacancy.province }}</span>
                  </p>
                </div>
                <div v-if="matching.vacancy?.mode">
                  <p class="text-gray-500 dark:text-gray-400">Modalidad</p>
                  <p class="font-medium text-gray-900 dark:text-white capitalize">{{ matching.vacancy.mode }}</p>
                </div>
                <div v-if="matching.vacancy?.cycle_required">
                  <p class="text-gray-500 dark:text-gray-400">Ciclo requerido</p>
                  <p class="font-medium text-gray-900 dark:text-white">{{ matching.vacancy.cycle_required }}</p>
                </div>
              </div>

              <div class="mt-4">
                <Link
                  :href="route('vacancies.show', matching.vacancy.id)"
                  class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                >
                  Ver vacante completa
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </div>
            </div>
          </div>

          <!-- Columna lateral - Acciones -->
          <div class="space-y-6">
            <!-- Estado del matching -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                Estado del Matching
              </h2>

              <div class="space-y-3">
                <div class="flex items-center gap-3">
                  <svg v-if="matching.student_matched" class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else class="h-6 w-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    Alumno confirmado
                  </span>
                </div>

                <div class="flex items-center gap-3">
                  <svg v-if="matching.company_matched" class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else class="h-6 w-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    Empresa confirmada
                  </span>
                </div>

                <div class="flex items-center gap-3">
                  <svg v-if="matching.validado_centro" class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <svg v-else class="h-6 w-6 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    Validado por el centro
                  </span>
                </div>
              </div>
            </div>

            <!-- Botón de validar -->
            <div v-if="!matching.validado_centro && matching.student_matched && matching.company_matched" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <button
                @click="showValidateModal = true"
                class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 text-base font-medium text-white hover:bg-green-700 transition-colors duration-200"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Validar Matching
              </button>

              <p class="mt-3 text-xs text-center text-gray-500 dark:text-gray-400">
                Al validar, el matching quedará confirmado y pasará a la zona de seguimiento
              </p>
            </div>

            <!-- Comentarios del centro (si están validados) -->
            <div v-if="matching.validado_centro && matching.comentarios_centro" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                Comentarios del Centro
              </h2>
              <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ matching.comentarios_centro }}
              </p>
            </div>

            <!-- Conversación -->
            <div v-if="hasConversation" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
              <div class="p-4 bg-gradient-to-r from-indigo-500 to-purple-600">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  Conversación
                </h2>
              </div>

              <!-- Mensajes -->
              <div class="p-4 max-h-96 overflow-y-auto space-y-3">
                <div v-if="!matching.conversation?.messages || matching.conversation.messages.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                  <svg class="h-12 w-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  <p class="text-sm">No hay mensajes aún</p>
                  <p class="text-xs mt-1">Inicia la conversación con el alumno y la empresa</p>
                </div>

                <div
                  v-for="message in matching.conversation?.messages"
                  :key="message.id"
                  class="flex flex-col gap-1"
                >
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-gray-900 dark:text-white">
                      {{ message.user?.name }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                      {{ new Date(message.created_at).toLocaleString('es-ES', { 
                        day: '2-digit', 
                        month: '2-digit', 
                        hour: '2-digit', 
                        minute: '2-digit' 
                      }) }}
                    </span>
                  </div>
                  <div class="bg-gray-100 dark:bg-gray-700 rounded-lg px-3 py-2">
                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ message.content }}</p>
                  </div>
                </div>
              </div>

              <!-- Formulario de envío -->
              <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                  <textarea
                    v-model="messageForm.content"
                    rows="2"
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm resize-none"
                    placeholder="Escribe un mensaje..."
                    :disabled="messageForm.processing"
                  ></textarea>
                  <button
                    type="submit"
                    :disabled="!messageForm.content.trim() || messageForm.processing"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 flex items-center gap-2"
                  >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                  </button>
                </form>
              </div>
            </div>

            <!-- Aviso si no hay conversación -->
            <div v-else class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-4">
              <div class="flex gap-3">
                <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div>
                  <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                    La conversación se creará automáticamente cuando ambas partes confirmen el matching
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de validación -->
    <div v-if="showValidateModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 transition-opacity" @click="showValidateModal = false"></div>
        
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-lg w-full p-6">
          <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Validar Matching
          </h3>

          <p class="text-gray-600 dark:text-gray-400 mb-6">
            ¿Confirmas que este alumno realizará sus prácticas en <strong class="text-gray-900 dark:text-white">{{ matching.company?.trade_name }}</strong> para la vacante <strong class="text-gray-900 dark:text-white">{{ matching.vacancy?.title }}</strong>?
          </p>

          <div class="mb-6">
            <label for="comentarios" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Comentarios (opcional)
            </label>
            <textarea
              id="comentarios"
              v-model="form.comentarios"
              rows="4"
              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              placeholder="Añade cualquier nota o comentario sobre esta validación..."
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-3">
            <button
              type="button"
              @click="showValidateModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
            >
              Cancelar
            </button>
            <button
              type="button"
              @click="validateMatching"
              :disabled="form.processing"
              class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-6 py-2 text-sm font-medium text-white hover:bg-green-700 disabled:opacity-50 transition-colors duration-200"
            >
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              Confirmar Validación
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

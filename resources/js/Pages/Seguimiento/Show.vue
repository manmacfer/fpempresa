<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3'
import { ref, computed, nextTick, onMounted, watch } from 'vue'

const props = defineProps({
  matching: { type: Object, required: true },
})

const page = usePage()
const activeTab = ref('resumen')
const chatContainer = ref(null)

// Forms
const messageForm = useForm({
  content: ''
})

const documentForm = useForm({
  file: null,
  type: 'other'
})

// Scroll to bottom of chat
const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight
    }
  })
}

// Watch for tab change to chat
watch(activeTab, (newTab) => {
  if (newTab === 'chat') {
    scrollToBottom()
  }
})

// Scroll to bottom when component mounts if chat tab is active
onMounted(() => {
  if (activeTab.value === 'chat') {
    scrollToBottom()
  }
})

// Send message
const sendMessage = () => {
  if (!props.matching.conversation) return
  
  messageForm.post(route('conversations.messages.store', props.matching.conversation.id), {
    preserveScroll: true,
    onSuccess: () => {
      messageForm.reset()
      scrollToBottom()
    }
  })
}

// Upload document
const handleFileChange = (event) => {
  documentForm.file = event.target.files[0]
}

const uploadDocument = () => {
  documentForm.post(route('matchings.documents.store', props.matching.id), {
    preserveScroll: true,
    onSuccess: () => {
      documentForm.reset()
      // Reset file input
      document.getElementById('document-upload').value = null
    }
  })
}

// Helper functions
const formatDate = (date) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatDateTime = (date) => {
  if (!date) return '—'
  return new Date(date).toLocaleString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getDocumentIcon = (type) => {
  const icons = {
    contract: '📄',
    report: '📊',
    certificate: '🎓',
    other: '📎'
  }
  return icons[type] || icons.other
}

const getDocumentTypeLabel = (type) => {
  const labels = {
    contract: 'Contrato',
    report: 'Informe',
    certificate: 'Certificado',
    other: 'Otro'
  }
  return labels[type] || labels.other
}

const messages = computed(() => {
  if (!props.matching.conversation || !props.matching.conversation.messages) {
    return []
  }
  return props.matching.conversation.messages
})

const documents = computed(() => props.matching.documents || [])

// Determine if message is from current user
const isMyMessage = (message) => {
  return message.user_id === page.props.auth.user.id
}

// Get message bubble colors based on sender role
const getUserRole = (message) => {
  const user = message.user
  if (user.student) return 'student'
  if (user.company) return 'company'
  if (user.teacher) return 'teacher'
  return 'default'
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Seguimiento: ${matching.student?.first_name || 'Estudiante'}`" />

    <div class="mx-auto max-w-6xl p-6">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            Seguimiento de Matching
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ matching.student?.first_name }} {{ matching.student?.last_name }} - {{ matching.company?.trade_name || matching.company?.legal_name || matching.company?.name || 'Empresa' }}
          </p>
        </div>
        <Link 
          :href="route('seguimiento.index')" 
          class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
        >
          Volver
        </Link>
      </div>

      <!-- Status Badge -->
      <div class="mb-6">
        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 dark:bg-green-900/30 dark:text-green-200">
          ✓ Validado por el centro
        </span>
        <span v-if="matching.validated_at" class="ml-2 text-sm text-gray-600 dark:text-gray-400">
          el {{ formatDate(matching.validated_at) }}
        </span>
      </div>

      <!-- Tabs -->
      <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
          <button
            @click="activeTab = 'resumen'"
            :class="[
              'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium',
              activeTab === 'resumen'
                ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
            ]"
          >
            Resumen
          </button>
          <button
            @click="activeTab = 'chat'"
            :class="[
              'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium',
              activeTab === 'chat'
                ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
            ]"
          >
            Chat
            <span v-if="messages.length" class="ml-2 rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">
              {{ messages.length }}
            </span>
          </button>
          <button
            @click="activeTab = 'documentos'"
            :class="[
              'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium',
              activeTab === 'documentos'
                ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
            ]"
          >
            Documentos
            <span v-if="documents.length" class="ml-2 rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">
              {{ documents.length }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div>
        <!-- Resumen Tab -->
        <div v-if="activeTab === 'resumen'" class="space-y-6">
          <!-- Student Info -->
          <section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Estudiante</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Nombre</p>
                <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                  {{ matching.student?.first_name }} {{ matching.student?.last_name }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                  {{ matching.student?.user?.email || '—' }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ciclo</p>
                <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                  {{ matching.student?.cycle || '—' }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Modalidad FP</p>
                <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                  {{ matching.student?.fp_modality || '—' }}
                </p>
              </div>
            </div>
          </section>

          <!-- Company Info -->
          <section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Empresa</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Nombre</p>
                <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                  {{ matching.company?.trade_name || matching.company?.legal_name || matching.company?.name || '—' }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                  {{ matching.company?.user?.email || '—' }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Sector</p>
                <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                  {{ matching.company?.sector || '—' }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Ubicación</p>
                <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                  {{ matching.company?.city || '—' }}<span v-if="matching.company?.city && matching.company?.province"> — </span>{{ matching.company?.province || '' }}
                </p>
              </div>
            </div>
          </section>

          <!-- Vacancy Info -->
          <section v-if="matching.vacancy" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Vacante</h3>
            <div class="space-y-3">
              <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Título</p>
                <p class="mt-1 font-medium text-gray-900 dark:text-gray-100">
                  {{ matching.vacancy.title || '—' }}
                </p>
              </div>
              <div v-if="matching.vacancy.description">
                <p class="text-sm text-gray-500 dark:text-gray-400">Descripción</p>
                <p class="mt-1 text-gray-700 dark:text-gray-300 whitespace-pre-line">
                  {{ matching.vacancy.description }}
                </p>
              </div>
            </div>
          </section>

          <!-- Comments from Center -->
          <section v-if="matching.comentarios_centro" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Comentarios del centro</h3>
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
              {{ matching.comentarios_centro }}
            </p>
          </section>
        </div>

        <!-- Chat Tab -->
        <div v-if="activeTab === 'chat'" class="space-y-4">
          <!-- Messages List -->
          <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <h3 class="border-b border-gray-200 p-6 text-lg font-semibold text-gray-900 dark:border-gray-700 dark:text-gray-100">Mensajes</h3>
            
            <div 
              ref="chatContainer"
              class="max-h-[500px] min-h-[400px] overflow-y-auto p-6"
            >
              <div v-if="messages.length === 0" class="flex h-full items-center justify-center text-center text-gray-500 dark:text-gray-400">
                No hay mensajes aún. ¡Inicia la conversación!
              </div>

              <div v-else class="space-y-4">
                <div 
                  v-for="message in messages" 
                  :key="message.id"
                  :class="[
                    'flex',
                    isMyMessage(message) ? 'justify-end' : 'justify-start'
                  ]"
                >
                  <div :class="['max-w-[70%] space-y-1', isMyMessage(message) ? 'items-end' : 'items-start']">
                    <!-- Sender name -->
                    <p 
                      v-if="!isMyMessage(message)"
                      :class="[
                        'text-xs font-medium',
                        getUserRole(message) === 'student' ? 'text-purple-700 dark:text-purple-300' : '',
                        getUserRole(message) === 'company' ? 'text-green-700 dark:text-green-300' : '',
                        getUserRole(message) === 'teacher' ? 'text-blue-700 dark:text-blue-300' : '',
                        getUserRole(message) === 'default' ? 'text-gray-700 dark:text-gray-300' : ''
                      ]"
                    >
                      {{ message.user?.name || 'Usuario' }}
                    </p>
                    
                    <!-- Message bubble -->
                    <div
                      :class="[
                        'rounded-2xl px-4 py-3 shadow-sm',
                        getUserRole(message) === 'student' ? 'bg-purple-100 dark:bg-purple-900/30' : '',
                        getUserRole(message) === 'company' ? 'bg-green-100 dark:bg-green-900/30' : '',
                        getUserRole(message) === 'teacher' ? 'bg-blue-100 dark:bg-blue-900/30' : '',
                        getUserRole(message) === 'default' ? 'bg-gray-100 dark:bg-gray-800' : '',
                        isMyMessage(message) ? 'rounded-tr-sm' : 'rounded-tl-sm'
                      ]"
                    >
                      <p class="text-sm whitespace-pre-line text-gray-900 dark:text-gray-100">
                        {{ message.content }}
                      </p>
                    </div>
                    
                    <!-- Timestamp -->
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ formatDateTime(message.created_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Send Message Form -->
          <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <form @submit.prevent="sendMessage" class="space-y-4">
              <div>
                <textarea
                  v-model="messageForm.content"
                  rows="3"
                  class="w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  placeholder="Escribe tu mensaje aquí..."
                  required
                  @keydown.enter.exact.prevent="sendMessage"
                ></textarea>
                <p v-if="messageForm.errors.content" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ messageForm.errors.content }}
                </p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  Presiona Enter para enviar
                </p>
              </div>
              <div class="flex justify-end">
                <button
                  type="submit"
                  :disabled="messageForm.processing || !messageForm.content.trim()"
                  class="rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  {{ messageForm.processing ? 'Enviando...' : 'Enviar mensaje' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Documentos Tab -->
        <div v-if="activeTab === 'documentos'" class="space-y-4">
          <!-- Documents List -->
          <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Documentos compartidos</h3>
            
            <div v-if="documents.length === 0" class="py-8 text-center text-gray-500 dark:text-gray-400">
              No hay documentos aún.
            </div>

            <div v-else class="space-y-3">
              <div 
                v-for="doc in documents" 
                :key="doc.id"
                class="flex items-center justify-between rounded-lg bg-gray-50 p-4 dark:bg-gray-800/60"
              >
                <div class="flex items-center gap-3">
                  <span class="text-2xl">{{ getDocumentIcon(doc.type) }}</span>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-gray-100">
                      {{ doc.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ getDocumentTypeLabel(doc.type) }} • 
                      Subido por {{ doc.uploader?.name || 'Usuario' }} • 
                      {{ formatDate(doc.created_at) }}
                    </p>
                  </div>
                </div>
                <a 
                  :href="route('documents.download', doc.id)"
                  class="rounded-lg bg-indigo-600 px-3 py-1 text-sm text-white hover:bg-indigo-700"
                >
                  Descargar
                </a>
              </div>
            </div>
          </div>

          <!-- Upload Document Form -->
          <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h4 class="mb-4 text-sm font-semibold text-gray-900 dark:text-gray-100">Subir documento</h4>
            <form @submit.prevent="uploadDocument" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Tipo de documento
                </label>
                <select
                  v-model="documentForm.type"
                  class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                >
                  <option value="contract">Contrato</option>
                  <option value="report">Informe</option>
                  <option value="certificate">Certificado</option>
                  <option value="other">Otro</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Archivo
                </label>
                <input
                  id="document-upload"
                  type="file"
                  @change="handleFileChange"
                  accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png"
                  class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  required
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  Formatos aceptados: PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, JPEG, PNG (máx. 10MB)
                </p>
                <p v-if="documentForm.errors.file" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ documentForm.errors.file }}
                </p>
              </div>
              <div class="flex justify-end">
                <button
                  type="submit"
                  :disabled="documentForm.processing || !documentForm.file"
                  class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
                >
                  {{ documentForm.processing ? 'Subiendo...' : 'Subir documento' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  students: Object,
  classroom: Object,
  filters: Object,
})

const pendingStudents = computed(() => {
  return props.students.data.filter(s => !s.validated)
})

const validatedStudents = computed(() => {
  return props.students.data.filter(s => s.validated)
})

const studentToDelete = ref(null)
const showDeleteModal = ref(false)

function validateStudent(studentId) {
  if (confirm('¿Estás seguro de que quieres validar a este alumno?')) {
    router.post(route('teacher.students.validate', studentId), {}, {
      preserveScroll: true,
    })
  }
}

function confirmDelete(student) {
  studentToDelete.value = student
  showDeleteModal.value = true
}

function deleteStudent() {
  if (studentToDelete.value) {
    router.delete(route('teacher.students.destroy', studentToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteModal.value = false
        studentToDelete.value = null
      }
    })
  }
}

function cancelDelete() {
  showDeleteModal.value = false
  studentToDelete.value = null
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Mis Alumnos" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
            Mis Alumnos
          </h1>
          <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            Classroom: <strong>{{ classroom.classroom_number }}</strong> - {{ classroom.nombre }}
          </p>
        </div>

        <!-- Estadísticas -->
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-lg dark:border-gray-700/50 dark:bg-gray-800">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total de alumnos</div>
            <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ students.total }}</div>
          </div>
          <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-6 shadow-lg dark:border-yellow-800 dark:bg-yellow-900/20">
            <div class="text-sm font-medium text-yellow-800 dark:text-yellow-400">Pendientes de validación</div>
            <div class="mt-2 text-3xl font-bold text-yellow-900 dark:text-yellow-300">{{ pendingStudents.length }}</div>
          </div>
          <div class="rounded-2xl border border-green-200 bg-green-50 p-6 shadow-lg dark:border-green-800 dark:bg-green-900/20">
            <div class="text-sm font-medium text-green-800 dark:text-green-400">Validados</div>
            <div class="mt-2 text-3xl font-bold text-green-900 dark:text-green-300">{{ validatedStudents.length }}</div>
          </div>
        </div>

        <!-- Alumnos pendientes de validación -->
        <div v-if="pendingStudents.length > 0" class="mb-8">
          <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
            <div class="p-6">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                Pendientes de validación
              </h2>
              <div class="space-y-4">
                <div
                  v-for="student in pendingStudents"
                  :key="student.id"
                  class="flex items-center justify-between rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20"
                >
                  <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-yellow-200 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 shadow-md">
                      <span class="text-xl font-bold">{{ student.first_name[0] }}{{ student.last_name[0] }}</span>
                    </div>
                    <div>
                      <div class="font-medium text-gray-900 dark:text-gray-100">
                        {{ student.first_name }} {{ student.last_name }}
                      </div>
                      <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ student.user.email }}
                        <span v-if="student.cycle" class="ml-2">· {{ student.cycle }}</span>
                      </div>
                    </div>
                  </div>
                  <div class="flex gap-2">
                    <button
                      @click="validateStudent(student.id)"
                      class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors duration-200"
                    >
                      ✓ Validar
                    </button>
                    <button
                      @click="confirmDelete(student)"
                      class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors duration-200"
                    >
                      ✕ Eliminar
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Alumnos validados -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 overflow-hidden">
          <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
              Alumnos validados ({{ validatedStudents.length }})
            </h2>

            <!-- Sin alumnos validados -->
            <div v-if="validatedStudents.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <p class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                No hay alumnos validados todavía
              </p>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Los alumnos validados aparecerán aquí
              </p>
            </div>

            <!-- Tarjetas de alumnos validados -->
            <div v-else class="space-y-4">
              <div
                v-for="student in validatedStudents"
                :key="student.id"
                class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200"
              >
                <!-- Avatar -->
                <div class="flex-shrink-0">
                  <img
                    v-if="student.avatar_path"
                    :src="`/storage/${student.avatar_path}`"
                    :alt="`${student.first_name} ${student.last_name}`"
                    class="h-14 w-14 rounded-xl object-cover shadow-md ring-2 ring-white dark:ring-gray-800"
                  />
                  <div
                    v-else
                    class="h-14 w-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-md ring-2 ring-white dark:ring-gray-800"
                  >
                    <span class="text-xl font-bold text-white">
                      {{ (student.first_name || '?')[0].toUpperCase() }}
                    </span>
                  </div>
                </div>

                <!-- Información del alumno -->
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    {{ student.first_name }} {{ student.last_name }}
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ student.cycle || 'Sin ciclo' }}
                    <span v-if="student.fp_modality"> · {{ student.fp_modality }}</span>
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ student.city || 'Sin ubicación' }}<span v-if="student.province">, {{ student.province }}</span>
                  </p>
                </div>

                <!-- Botones de acción -->
                <div class="flex flex-shrink-0 items-center gap-2">
                  <Link
                    :href="route('students.public.show', student.id)"
                    class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200"
                  >
                    Perfil
                  </Link>

                  <Link
                    :href="route('teacher.students.applications', student.id)"
                    class="inline-flex items-center rounded-lg border border-blue-300 bg-white px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-50 dark:border-blue-700 dark:bg-gray-700 dark:text-blue-400 dark:hover:bg-blue-900/20 transition-colors duration-200"
                  >
                    Candidaturas
                  </Link>

                  <Link
                    :href="route('teacher.students.matches', student.id)"
                    class="inline-flex items-center rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 transition-colors duration-200"
                  >
                    Matchings
                  </Link>

                  <button
                    @click="confirmDelete(student)"
                    class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 transition-colors duration-200"
                  >
                    Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.self="cancelDelete"
      >
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 p-6">
          <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
              <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Confirmar eliminación
              </h3>
              <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                ¿Estás seguro de que quieres eliminar a
                <strong class="text-gray-900 dark:text-white">{{ studentToDelete?.first_name }} {{ studentToDelete?.last_name }}</strong>?
              </p>
              <p class="mt-1 text-sm text-red-600 dark:text-red-400 font-medium">
                Esta acción no se puede deshacer y eliminará toda la información del alumno.
              </p>
            </div>
          </div>

          <div class="mt-6 flex gap-3 justify-end">
            <button
              @click="cancelDelete"
              class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200"
            >
              Cancelar
            </button>
            <button
              @click="deleteStudent"
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 rounded-lg transition-colors duration-200"
            >
              Sí, eliminar
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </AuthenticatedLayout>
</template>

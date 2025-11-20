<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  student: Object
})

// Formatear disponibilidad
const formatAvailabilitySlot = (slot) => {
  if (!slot) return null
  if (slot === 'manana') return 'Mañana'
  if (slot === 'tarde') return 'Tarde'
  if (slot === 'completa') return 'Completa'
  return slot
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Perfil de ${student.first_name} ${student.last_name}`" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
          <Link
            :href="route('teacher.my-students')"
            class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 mb-4"
          >
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Volver a Mis Alumnos
          </Link>

          <div class="flex items-center gap-6">
            <!-- Avatar -->
            <div class="flex-shrink-0">
              <img
                v-if="student.avatar_path"
                :src="`/storage/${student.avatar_path}`"
                :alt="`${student.first_name} ${student.last_name}`"
                class="h-32 w-32 rounded-2xl object-cover shadow-xl ring-4 ring-white dark:ring-gray-800"
              />
              <div
                v-else
                class="h-32 w-32 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-xl ring-4 ring-white dark:ring-gray-800"
              >
                <span class="text-5xl font-bold text-white">
                  {{ (student.first_name || '?')[0].toUpperCase() }}
                </span>
              </div>
            </div>

            <!-- Info básica -->
            <div class="flex-1">
              <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                {{ student.first_name }} {{ student.last_name }}
              </h1>
              <p class="mt-2 text-xl text-gray-600 dark:text-gray-400">
                {{ student.cycle || 'Sin ciclo especificado' }}
                <span v-if="student.fp_modality"> · {{ student.fp_modality }}</span>
              </p>
              <div class="mt-3 flex flex-wrap gap-3">
                <Link
                  :href="route('teacher.students.applications', student.id)"
                  class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors duration-200"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  Ver Candidaturas
                </Link>
                
                <Link
                  :href="route('teacher.students.matches', student.id)"
                  class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 transition-colors duration-200"
                >
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Ver Matchings
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Contenido del perfil -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Columna principal -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Información personal -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Información Personal
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-if="student.phone">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</p>
                  <p class="mt-1 text-base text-gray-900 dark:text-white">{{ student.phone }}</p>
                </div>
                
                <div v-if="student.user?.email">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</p>
                  <p class="mt-1 text-base text-gray-900 dark:text-white">{{ student.user.email }}</p>
                </div>
                
                <div v-if="student.city">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ubicación</p>
                  <p class="mt-1 text-base text-gray-900 dark:text-white">
                    {{ student.city }}<span v-if="student.province">, {{ student.province }}</span>
                  </p>
                </div>
                
                <div v-if="student.center">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Centro Educativo</p>
                  <p class="mt-1 text-base text-gray-900 dark:text-white">{{ student.center }}</p>
                </div>
              </div>
            </div>

            <!-- Competencias técnicas -->
            <div v-if="student.tech_competencies?.length" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Competencias Técnicas
              </h2>
              
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="tech in student.tech_competencies"
                  :key="tech"
                  class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-sm font-medium text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200"
                >
                  {{ tech }}
                </span>
              </div>
            </div>

            <!-- Idiomas -->
            <div v-if="student.languages?.length" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Idiomas
              </h2>
              
              <div class="space-y-2">
                <div
                  v-for="(lang, index) in student.languages"
                  :key="index"
                  class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
                >
                  <span class="font-medium text-gray-900 dark:text-white">{{ lang.name }}</span>
                  <span class="text-sm text-gray-600 dark:text-gray-400">{{ lang.level }}</span>
                </div>
              </div>
            </div>

            <!-- Experiencia -->
            <div v-if="student.experiences?.length" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Experiencia Profesional
              </h2>
              
              <div class="space-y-4">
                <div
                  v-for="exp in student.experiences"
                  :key="exp.id"
                  class="border-l-4 border-indigo-500 pl-4"
                >
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ exp.position }}</h3>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ exp.company }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-500">
                    {{ exp.start_date }} - {{ exp.end_date || 'Actualidad' }}
                  </p>
                  <p v-if="exp.description" class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ exp.description }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Formación -->
            <div v-if="student.educations?.length" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                Formación Académica
              </h2>
              
              <div class="space-y-4">
                <div
                  v-for="edu in student.educations"
                  :key="edu.id"
                  class="border-l-4 border-purple-500 pl-4"
                >
                  <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ edu.degree }}</h3>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ edu.institution }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-500">
                    {{ edu.start_date }} - {{ edu.end_date || 'Actualidad' }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Columna lateral -->
          <div class="space-y-6">
            <!-- Disponibilidad -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                Disponibilidad
              </h2>
              
              <div class="space-y-3">
                <div v-if="student.availability_slot">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jornada</p>
                  <p class="mt-1 text-base text-gray-900 dark:text-white capitalize">{{ formatAvailabilitySlot(student.availability_slot) }}</p>
                </div>
                
                <div v-if="student.available_from">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Disponible desde</p>
                  <p class="mt-1 text-base text-gray-900 dark:text-white">
                    {{ new Date(student.available_from).toLocaleDateString('es-ES') }}
                  </p>
                </div>
                
                <div v-if="student.work_modalities?.length">
                  <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Modalidades</p>
                  <div class="mt-1 flex flex-wrap gap-1">
                    <span
                      v-for="modality in student.work_modalities"
                      :key="modality"
                      class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-200"
                    >
                      {{ modality }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Transporte -->
            <div v-if="student.has_driver_license || student.has_vehicle" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                Transporte
              </h2>
              
              <div class="space-y-2">
                <div v-if="student.has_driver_license" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                  <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Carnet de conducir
                </div>
                
                <div v-if="student.has_vehicle" class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                  <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Vehículo propio
                </div>
              </div>
            </div>

            <!-- Habilidades blandas -->
            <div v-if="student.soft_skills?.length" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6">
              <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                Habilidades Blandas
              </h2>
              
              <div class="space-y-2">
                <span
                  v-for="skill in student.soft_skills"
                  :key="skill"
                  class="block text-sm text-gray-700 dark:text-gray-300"
                >
                  • {{ skill }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

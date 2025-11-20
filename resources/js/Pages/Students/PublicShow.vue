<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  student: { type: Object, required: true },
})

const page = usePage()

const displayName = computed(() => props.student.user_name || 'Alumno')

// Verificar si el usuario actual es el dueño del perfil
const isOwner = computed(() => {
  const authStudentId = page.props.auth?.studentId
  return authStudentId && authStudentId === props.student.id
})

function fmtDate(iso) {
  if (!iso) return null
  const [y, m, d] = iso.split('-')
  return `${d}/${m}/${y}`
}

const formatWorkModality = (mod) => {
  if (!mod) return null
  if (mod === 'presencial') return 'Presencial'
  if (mod === 'remota') return 'Remota'
  if (mod === 'hibrida' || mod === 'híbrida') return 'Híbrida'
  if (mod === 'indiferente') return 'Indiferente'
  return mod
}

const hasPersonal = computed(() =>
  props.student.city || props.student.birth_date || props.student.address || props.student.postal_code || props.student.phone || props.student.dni || props.student.province || props.student.municipality || props.student.has_driver_license || props.student.has_vehicle
)

const hasAcademic = computed(() =>
  props.student.cycle || props.student.center || props.student.year_start || props.student.year_end || props.student.fp_modality
)

const hasAvailability = computed(() =>
  props.student.availability_slot || props.student.work_modality || props.student.remote_days != null || props.student.days_per_week != null || props.student.available_from || props.student.relocate || (props.student.relocate_cities && props.student.relocate_cities.length) || props.student.transport_own || (props.student.commitments && props.student.commitments.length)
)

const hasInterests = computed(() =>
  (props.student.sectors && props.student.sectors.length) ||
  props.student.preferred_company_type ||
  props.student.non_formal_experience ||
  (props.student.tech_competencies && props.student.tech_competencies.length) ||
  (props.student.soft_skills && props.student.soft_skills.length) ||
  (props.student.languages && props.student.languages.length) ||
  (props.student.certifications && props.student.certifications.length)
)

const hasExtra = computed(() =>
  props.student.hobbies || props.student.clubs || props.student.align_activities || props.student.entrepreneurship
)

const hasLinks = computed(() =>
  props.student.link_linkedin || props.student.link_portfolio || props.student.link_github || props.student.link_video
)

const hasDocuments = computed(() =>
  props.student.avatar_url || props.student.cv_url || props.student.cover_letter_url || (props.student.other_certs && props.student.other_certs.length > 0)
)
</script>

<template>
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Header con gradiente -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-100 bg-gradient-to-br from-indigo-600 to-purple-600 p-8 shadow-xl dark:border-gray-800 dark:from-indigo-700 dark:to-purple-700">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-white">Perfil Público</h1>
              <p class="mt-2 text-indigo-100">{{ isOwner ? 'Visualización de tu perfil profesional' : 'Perfil del estudiante' }}</p>
            </div>
            <Link 
              v-if="isOwner"
              :href="route('students.edit.me')" 
              class="inline-flex items-center gap-2 rounded-xl border-2 border-white/30 bg-white/20 px-5 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:bg-white/30"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              Editar perfil
            </Link>
          </div>
        </div>

        <!-- Card de presentación -->
        <section class="mb-6 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900">
          <div class="p-8">
            <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-start">
              <div class="flex-shrink-0">
                <img
                  v-if="student.avatar_url"
                  :src="student.avatar_url"
                  alt="avatar"
                  class="h-32 w-32 rounded-2xl object-cover shadow-lg ring-4 ring-indigo-100 dark:ring-indigo-900/50"
                />
                <div
                  v-else
                  class="flex h-32 w-32 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 text-5xl font-bold text-indigo-600 shadow-lg ring-4 ring-indigo-100 dark:from-indigo-900/50 dark:to-purple-900/50 dark:text-indigo-300 dark:ring-indigo-900/50"
                >
                  {{ displayName.slice(0,1) }}
                </div>
              </div>

              <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ displayName }}</h2>
                
                <div class="mt-4 flex flex-wrap items-center gap-3">
                  <span v-if="student.cycle" class="inline-flex items-center gap-1.5 rounded-full bg-indigo-100 px-3 py-1.5 text-sm font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ student.cycle }}
                  </span>

                  <span v-if="student.center" class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    {{ student.center }}
                  </span>

                  <span v-if="student.city" class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{ student.city }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </section>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <!-- Main column -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Documentos -->
            <section v-if="hasDocuments" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
              <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
                <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                  <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Documentos
                </h3>
              </div>

              <div class="p-6 space-y-3">
              <!-- CV Auto-generado (siempre disponible) -->
              <a :href="route('students.cv.download', student.id)" class="flex items-center justify-between rounded-xl border-2 border-indigo-200 bg-gradient-to-r from-indigo-50 to-purple-50 p-4 transition-all hover:scale-[1.02] hover:shadow-lg hover:border-indigo-300 dark:border-indigo-800 dark:from-indigo-950/40 dark:to-purple-950/30 dark:hover:border-indigo-600">
                <div class="flex items-center gap-3">
                  <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  <div>
                    <span class="block font-semibold text-gray-900 dark:text-white">Descargar Currículum (CV)</span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">Generado automáticamente desde el perfil</span>
                  </div>
                </div>
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </a>

              <a v-if="student.cover_letter_url" :href="student.cover_letter_url" target="_blank" class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 p-3 transition-all hover:border-indigo-300 hover:bg-indigo-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:border-indigo-600 dark:hover:bg-indigo-900/20">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <span class="font-medium text-gray-900 dark:text-white">Carta de presentación</span>
              </a>

              <div v-if="student.other_certs && student.other_certs.length" class="pt-1">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Otros certificados</div>
                <ul class="grid gap-2 sm:grid-cols-2">
                  <li v-for="(cert, i) in student.other_certs" :key="i" class="flex items-center gap-3 rounded-lg bg-gray-50 p-2 text-sm dark:bg-gray-800/60">
                    <a :href="cert.url" target="_blank" class="truncate text-sm text-indigo-600 hover:underline dark:text-indigo-400">{{ cert.name || `Certificado ${i + 1}` }}</a>
                  </li>
                </ul>
              </div>
            </div>
          </section>

          <!-- Intereses y perfil -->
          <section v-if="hasInterests" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
              <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                Intereses y perfil
              </h3>
            </div>

            <div class="p-6 space-y-5">
              <div v-if="student.sectors?.length" class="flex flex-wrap gap-2">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 w-full">Sectores</div>
                <div class="flex flex-wrap gap-2">
                  <span v-for="(s,i) in student.sectors" :key="i" class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ s }}</span>
                </div>
              </div>

              <!-- Presentación (antes Experiencia no formal) - Ancho completo -->
              <div v-if="student.non_formal_experience">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Presentación</div>
                <div class="mt-1 text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ student.non_formal_experience }}</div>
              </div>

              <!-- Tipos de empresas de interés (antes Tipo de empresa) - Ancho completo -->
              <div v-if="student.preferred_company_type">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Tipos de empresas de interés</div>
                <div class="mt-1 text-sm text-gray-800 dark:text-gray-200 capitalize">{{ student.preferred_company_type }}</div>
              </div>

              <div v-if="student.tech_competencies && student.tech_competencies.length" class="mt-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                  Tecnologías
                </h3>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="t in student.tech_competencies"
                    :key="t"
                    class="px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200"
                  >
                    {{ t }}
                  </span>
                </div>
              </div>

              <div v-if="student.soft_skills?.length">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Habilidades personales</div>
                <div class="mt-2 flex flex-wrap gap-2">
                  <span v-for="(s,i) in student.soft_skills" :key="i" class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ s }}</span>
                </div>
              </div>

              <div v-if="student.languages?.length">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Idiomas</div>
                <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                  <div v-for="(lng,i) in student.languages" :key="i" class="rounded-lg bg-gray-50 p-2 text-sm dark:bg-gray-800/60">
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ lng.name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400" v-if="lng.level">{{ lng.level }}</div>
                  </div>
                </div>
              </div>

              <div v-if="student.certifications?.length">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Certificaciones</div>
                <div class="mt-2 flex flex-wrap gap-2">
                  <span v-for="(c,i) in student.certifications" :key="i" class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ c }}</span>
                </div>
              </div>
            </div>
          </section>

          <!-- Extra -->
          <section v-if="hasExtra" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Sobre mí</h3>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
              <div v-if="student.hobbies">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Aficiones</div>
                <div class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ student.hobbies }}</div>
              </div>

              <div v-if="student.clubs">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Clubes / Asociaciones</div>
                <div class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ student.clubs }}</div>
              </div>

              <div v-if="student.entrepreneurship">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Emprendimiento</div>
                <div class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ student.entrepreneurship }}</div>
              </div>

              <div v-if="student.align_activities != null" class="flex items-center justify-between gap-4">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Actividades alineadas</div>
                <div class="text-sm text-gray-800 dark:text-gray-200">{{ student.align_activities ? 'Sí' : 'No' }}</div>
              </div>
            </div>
          </section>

          <!-- Experiencia -->
          <section v-if="student.experiences?.length" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Experiencia</h3>
              <span class="text-xs text-gray-400">{{ student.experiences.length }} item(s)</span>
            </div>

            <div class="mt-4 space-y-3">
              <article v-for="x in student.experiences" :key="x.id" class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/60">
                <div class="flex items-start justify-between">
                  <div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ x.company }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400" v-if="x.position">{{ x.position }}</div>
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ fmtDate(x.start_date) || '—' }} — {{ fmtDate(x.end_date) || 'Actual' }}
                  </div>
                </div>

                <div class="mt-2 text-sm text-gray-800 dark:text-gray-200" v-if="x.functions">{{ x.functions }}</div>
                <div v-if="x.is_non_formal" class="mt-2 text-xs inline-block rounded bg-yellow-100 px-2 py-0.5 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200">No formal</div>
              </article>
            </div>
          </section>

          <!-- Formación -->
          <section v-if="student.educations?.length" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Formación</h3>
              <span class="text-xs text-gray-400">{{ student.educations.length }} item(s)</span>
            </div>

            <div class="mt-4 space-y-3">
              <article v-for="e in student.educations" :key="e.id" class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/60">
                <div class="font-medium text-gray-900 dark:text-gray-100">{{ e.title }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                  <span v-if="e.center">{{ e.center }} · </span>{{ fmtDate(e.start_date) || '—' }} — {{ fmtDate(e.end_date) || 'Actual' }}
                </div>
              </article>
            </div>
          </section>

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Datos personales -->
          <section v-if="hasPersonal" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-3 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
              <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Datos personales
              </h3>
            </div>
            <dl class="p-5 grid grid-cols-1 gap-3 text-sm">
              <div v-if="student.dni" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">DNI/NIE</dt><dd>{{ student.dni }}</dd></div>
              <div v-if="student.phone" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Teléfono</dt><dd>{{ student.phone }}</dd></div>
              <div v-if="student.birth_date" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Nacimiento</dt><dd>{{ fmtDate(student.birth_date) }}</dd></div>
              <div v-if="student.address" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Dirección</dt><dd class="text-right">{{ student.address }}</dd></div>
              <div v-if="student.postal_code" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">C. Postal</dt><dd>{{ student.postal_code }}</dd></div>
              <div v-if="student.city" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Municipio</dt><dd>{{ student.city }}</dd></div>
              <div v-if="student.municipality" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Población</dt><dd>{{ student.municipality }}</dd></div>
              <div v-if="student.province" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Provincia</dt><dd>{{ student.province }}</dd></div>
              <div v-if="student.has_driver_license != null" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Carnet conducir</dt><dd>{{ student.has_driver_license ? 'Sí' : 'No' }}</dd></div>
              <div v-if="student.has_vehicle != null" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Vehículo propio</dt><dd>{{ student.has_vehicle ? 'Sí' : 'No' }}</dd></div>
            </dl>
          </section>

          <!-- Académicos -->
          <section v-if="hasAcademic" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-3 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
              <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Académicos
              </h3>
            </div>
            <dl class="p-5 grid grid-cols-1 gap-3 text-sm">
              <div v-if="student.cycle" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Ciclo</dt><dd>{{ student.cycle }}</dd></div>
              <div v-if="student.center" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Centro</dt><dd>{{ student.center }}</dd></div>
              <div v-if="student.year_start || student.year_end" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Años</dt>
                <dd>{{ student.year_start ? student.year_start : '—' }}<span v-if="student.year_start && student.year_end"> — </span>{{ student.year_end ? student.year_end : '' }}</dd>
              </div>
              <div v-if="student.fp_modality" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Modalidad FP</dt><dd class="capitalize">{{ student.fp_modality === 'dual' ? 'Dual' : 'General' }}</dd></div>
            </dl>
          </section>

          <!-- Disponibilidad -->
          <section v-if="hasAvailability" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-3 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
              <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Disponibilidad
              </h3>
            </div>
            <dl class="p-5 grid grid-cols-1 gap-3 text-sm">
              <div v-if="student.availability_slot" class="flex justify-between text-gray-900 dark:text-gray-100">
                <dt class="text-gray-500 dark:text-gray-400">Disponibilidad</dt>
                <dd>{{ student.availability_slot === 'completa' ? 'Completa' : (student.availability_slot === 'manana' ? 'Mañana' : 'Tarde') }}</dd>
              </div>

              <div v-if="student.work_modality" class="flex justify-between text-gray-900 dark:text-gray-100">
                <dt class="text-gray-500 dark:text-gray-400">Modalidad</dt>
                <dd>{{ formatWorkModality(student.work_modality) }}</dd>
              </div>

              <div v-if="student.work_modality === 'hibrida' && student.remote_days != null" class="flex justify-between text-gray-900 dark:text-gray-100">
                <dt class="text-gray-500 dark:text-gray-400">Días remoto</dt>
                <dd>{{ student.remote_days }}</dd>
              </div>

              <div v-if="student.days_per_week != null" class="flex justify-between text-gray-900 dark:text-gray-100">
                <dt class="text-gray-500 dark:text-gray-400">Días/semana</dt>
                <dd>{{ student.days_per_week }}</dd>
              </div>

              <div v-if="student.available_from" class="flex justify-between text-gray-900 dark:text-gray-100">
                <dt class="text-gray-500 dark:text-gray-400">Disponible desde</dt>
                <dd>{{ fmtDate(student.available_from) }}</dd>
              </div>

              <div v-if="student.transport_own != null" class="flex justify-between text-gray-900 dark:text-gray-100">
                <dt class="text-gray-500 dark:text-gray-400">Transporte propio</dt>
                <dd>{{ student.transport_own ? 'Sí' : 'No' }}</dd>
              </div>

              <div v-if="student.relocate != null" class="flex justify-between text-gray-900 dark:text-gray-100">
                <dt class="text-gray-500 dark:text-gray-400">Se desplaza</dt>
                <dd>{{ student.relocate ? 'Sí' : 'No' }}</dd>
              </div>

              <!-- Ciudades y Compromisos como bloques separados para mejor espaciado -->
              <div v-if="student.relocate_cities?.length" class="text-sm">
                <dt class="text-gray-500 dark:text-gray-400 mb-2">Ciudades</dt>
                <dd class="flex flex-wrap gap-2">
                  <span v-for="(c,i) in student.relocate_cities" :key="i" class="rounded-full bg-gray-100 px-2 py-1 text-[11px] text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ c }}</span>
                </dd>
              </div>

              <div v-if="student.commitments?.length" class="text-sm">
                <dt class="text-gray-500 dark:text-gray-400 mb-2">Compromisos</dt>
                <dd class="flex flex-wrap gap-2">
                  <span v-for="(c,i) in student.commitments" :key="i" class="rounded-full bg-yellow-100 px-2 py-1 text-[11px] text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200">{{ c }}</span>
                </dd>
              </div>
            </dl>
          </section>

          <!-- Enlaces -->
          <section v-if="hasLinks" class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-3 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
              <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
                Enlaces
              </h3>
            </div>
            <ul class="p-5 space-y-2 text-sm">
              <li v-if="student.link_linkedin"><a :href="student.link_linkedin" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">LinkedIn</a></li>
              <li v-if="student.link_portfolio"><a :href="student.link_portfolio" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">Portfolio</a></li>
              <li v-if="student.link_github"><a :href="student.link_github" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">GitHub</a></li>
              <li v-if="student.link_video"><a :href="student.link_video" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">Vídeo presentación</a></li>
            </ul>
          </section>
        </div>
      </div>

      <p v-if="!hasPersonal && !hasAcademic && !hasAvailability && !hasInterests && !hasExtra && !student.educations?.length && !student.experiences?.length && !hasLinks && !hasDocuments"
         class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        Aún no hay información pública para este perfil.
      </p>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
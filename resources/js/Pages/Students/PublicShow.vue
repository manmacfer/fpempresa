<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  student: { type: Object, required: true },
})

const displayName = computed(() => props.student.user_name || 'Alumno')

function fmtDate(iso) {
  if (!iso) return null
  const [y, m, d] = iso.split('-')
  return `${d}/${m}/${y}`
}

const hasPersonal = computed(() =>
  props.student.city || props.student.birth_date || props.student.address || props.student.postal_code || props.student.phone || props.student.dni
)

const hasAcademic = computed(() =>
  props.student.cycle || props.student.center || props.student.year_start || props.student.year_end || props.student.fp_modality
)

const hasAvailability = computed(() =>
  props.student.availability_slot || props.student.work_modality || props.student.remote_days != null || props.student.days_per_week != null || props.student.available_from || props.student.relocate || (props.student.relocate_cities && props.student.relocate_cities.length) || props.student.transport_own
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
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
          Perfil público
        </h2>
        <Link :href="route('students.edit.me')" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
          Editar mi perfil
        </Link>
      </div>
    </template>

    <div class="mx-auto max-w-5xl p-6">
      <!-- HEAD -->
      <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-4">
          <img
            v-if="student.avatar_url"
            :src="student.avatar_url"
            alt="avatar"
            class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
          />
          <div
            v-else
            class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 text-2xl font-semibold text-gray-600 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700"
          >
            {{ displayName.slice(0,1) }}
          </div>

          <div class="min-w-0">
            <h1 class="truncate text-2xl font-bold text-gray-900 dark:text-gray-100">
              {{ displayName }}
            </h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
              <span v-if="student.cycle">{{ student.cycle }}</span>
              <span v-if="student.cycle && student.center"> · </span>
              <span v-if="student.center">{{ student.center }}</span>
              <span v-if="(student.cycle || student.center) && student.city"> · </span>
              <span v-if="student.city">{{ student.city }}</span>
            </p>
          </div>
        </div>
      </section>

      <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Columna principal -->
        <div class="md:col-span-2 space-y-6">
          <!-- Intereses y perfil -->
          <section v-if="hasInterests" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Intereses y perfil</h3>

            <div v-if="student.sectors?.length" class="mb-3">
              <div class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Sectores de interés</div>
              <div class="flex flex-wrap gap-2">
                <span v-for="(s,i) in student.sectors" :key="i" class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                  {{ s }}
                </span>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
              <div v-if="student.preferred_company_type">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Tipo de empresa</div>
                <div class="text-sm text-gray-800 dark:text-gray-200">{{ student.preferred_company_type }}</div>
              </div>
              <div v-if="student.non_formal_experience">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Experiencia no formal</div>
                <div class="text-sm text-gray-800 dark:text-gray-200">{{ student.non_formal_experience }}</div>
              </div>
            </div>

            <div v-if="student.tech_competencies?.length" class="mt-3">
              <div class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Competencias técnicas</div>
              <div class="flex flex-wrap gap-2">
                <span v-for="(t,i) in student.tech_competencies" :key="i" class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                  {{ t }}
                </span>
              </div>
            </div>

            <div v-if="student.soft_skills?.length" class="mt-3">
              <div class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Habilidades personales</div>
              <div class="flex flex-wrap gap-2">
                <span v-for="(s,i) in student.soft_skills" :key="i" class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                  {{ s }}
                </span>
              </div>
            </div>

            <div v-if="student.languages?.length" class="mt-3">
              <div class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Idiomas</div>
              <ul class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                <li v-for="(lng,i) in student.languages" :key="i" class="rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-800 dark:bg-gray-800/60 dark:text-gray-200">
                  <strong>{{ lng.name }}</strong><span v-if="lng.level"> · {{ lng.level }}</span>
                </li>
              </ul>
            </div>

            <div v-if="student.certifications?.length" class="mt-3">
              <div class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Certificaciones</div>
              <div class="flex flex-wrap gap-2">
                <span v-for="(c,i) in student.certifications" :key="i" class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                  {{ c }}
                </span>
              </div>
            </div>
          </section>

          <!-- Experiencia -->
          <section v-if="student.experiences?.length" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Experiencia</h3>
            <div class="space-y-3">
              <article v-for="x in student.experiences" :key="x.id" class="rounded-lg bg-gray-50 p-3 text-sm dark:bg-gray-800/60">
                <div class="font-medium text-gray-900 dark:text-gray-100">
                  {{ x.company }} <span v-if="x.position" class="text-gray-500 dark:text-gray-400">· {{ x.position }}</span>
                </div>
                <div class="text-gray-500 dark:text-gray-400">
                  {{ fmtDate(x.start_date) || '—' }} — {{ fmtDate(x.end_date) || 'Actual' }}
                  <span v-if="x.is_non_formal" class="ms-2 rounded bg-gray-200 px-2 py-0.5 text-[11px] text-gray-700 dark:bg-gray-700 dark:text-gray-200">No formal</span>
                </div>
                <div v-if="x.functions" class="mt-1 text-gray-800 dark:text-gray-200">
                  {{ x.functions }}
                </div>
              </article>
            </div>
          </section>

          <!-- Formación -->
          <section v-if="student.educations?.length" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Formación</h3>
            <div class="space-y-3">
              <article v-for="e in student.educations" :key="e.id" class="rounded-lg bg-gray-50 p-3 text-sm dark:bg-gray-800/60">
                <div class="font-medium text-gray-900 dark:text-gray-100">{{ e.title }}</div>
                <div class="text-gray-500 dark:text-gray-400">
                  <span v-if="e.center">{{ e.center }} · </span>{{ fmtDate(e.start_date) || '—' }} — {{ fmtDate(e.end_date) || 'Actual' }}
                </div>
              </article>
            </div>
          </section>
        </div>

        <!-- Columna lateral -->
        <div class="space-y-6">
          <!-- Datos personales -->
          <section v-if="hasPersonal" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Datos</h3>
            <dl class="grid grid-cols-1 gap-2 text-sm">
              <div v-if="student.city" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Ciudad</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ student.city }}</dd>
              </div>
              <div v-if="student.birth_date" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Nacimiento</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ fmtDate(student.birth_date) }}</dd>
              </div>
              <div v-if="student.address" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Dirección</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ student.address }}</dd>
              </div>
              <div v-if="student.postal_code" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">C. Postal</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ student.postal_code }}</dd>
              </div>
              <div v-if="student.phone" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Teléfono</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ student.phone }}</dd>
              </div>
              <div v-if="student.dni" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">DNI/NIE</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ student.dni }}</dd>
              </div>
            </dl>
          </section>

          <!-- Académicos -->
          <section v-if="hasAcademic" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Académicos</h3>
            <dl class="grid grid-cols-1 gap-2 text-sm">
              <div v-if="student.cycle" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Ciclo</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ student.cycle }}</dd>
              </div>
              <div v-if="student.center" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Centro</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ student.center }}</dd>
              </div>
              <div v-if="student.year_start || student.year_end" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Años</dt>
                <dd class="text-gray-900 dark:text-gray-100">
                  <span v-if="student.year_start">{{ student.year_start }}</span>
                  <span v-if="student.year_start && student.year_end"> — </span>
                  <span v-if="student.year_end">{{ student.year_end }}</span>
                </dd>
              </div>
              <div v-if="student.fp_modality" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Modalidad FP</dt>
                <dd class="text-gray-900 dark:text-gray-100 capitalize">{{ student.fp_modality }}</dd>
              </div>
            </dl>
          </section>

          <!-- Disponibilidad -->
          <section v-if="hasAvailability" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Disponibilidad</h3>
            <dl class="grid grid-cols-1 gap-2 text-sm">
              <div v-if="student.availability_slot" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Franja</dt>
                <dd class="text-gray-900 dark:text-gray-100 capitalize">
                  {{ student.availability_slot === 'both' ? 'Ambas' : (student.availability_slot === 'morning' ? 'Mañana' : 'Tarde') }}
                </dd>
              </div>
              <div v-if="student.work_modality" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Modalidad</dt>
                <dd class="text-gray-900 dark:text-gray-100 capitalize">{{ student.work_modality }}</dd>
              </div>
              <div v-if="student.remote_days != null" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Días remoto</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ student.remote_days }}</dd>
              </div>
              <div v-if="student.days_per_week != null" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Días/semana</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ student.days_per_week }}</dd>
              </div>
              <div v-if="student.available_from" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Desde</dt>
                <dd class="text-gray-900 dark:text-gray-100">{{ fmtDate(student.available_from) }}</dd>
              </div>
              <div v-if="student.transport_own" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Transporte propio</dt>
                <dd class="text-gray-900 dark:text-gray-100">Sí</dd>
              </div>
              <div v-if="student.relocate" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Se desplaza</dt>
                <dd class="text-gray-900 dark:text-gray-100">Sí</dd>
              </div>
              <div v-if="student.relocate_cities?.length" class="flex items-start justify-between gap-4">
                <dt class="text-gray-500 dark:text-gray-400">Ciudades</dt>
                <dd class="text-right">
                  <div class="flex flex-wrap justify-end gap-2">
                    <span v-for="(c,i) in student.relocate_cities" :key="i" class="rounded-full bg-gray-100 px-2 py-1 text-[11px] text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ c }}</span>
                  </div>
                </dd>
              </div>
            </dl>
          </section>

          <!-- Enlaces -->
          <section v-if="hasLinks" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Enlaces</h3>
            <ul class="space-y-2 text-sm">
              <li v-if="student.link_linkedin"><a :href="student.link_linkedin" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">LinkedIn</a></li>
              <li v-if="student.link_portfolio"><a :href="student.link_portfolio" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">Portfolio</a></li>
              <li v-if="student.link_github"><a :href="student.link_github" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">GitHub</a></li>
              <li v-if="student.link_video"><a :href="student.link_video" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">Vídeo</a></li>
            </ul>
          </section>
        </div>
      </div>

      <p v-if="!hasPersonal && !hasAcademic && !hasAvailability && !hasInterests && !student.educations?.length && !student.experiences?.length && !hasLinks"
         class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        Aún no hay información pública para este perfil.
      </p>
    </div>
  </AuthenticatedLayout>
</template>

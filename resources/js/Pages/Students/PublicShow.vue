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

    <div class="mx-auto max-w-5xl p-6 space-y-6">
      <!-- HEAD -->
      <section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-5">
          <div class="flex-shrink-0">
            <img
              v-if="student.avatar_url"
              :src="student.avatar_url"
              alt="avatar"
              class="h-24 w-24 rounded-full object-cover ring-2 ring-white shadow-sm dark:ring-gray-800"
            />
            <div
              v-else
              class="flex h-24 w-24 items-center justify-center rounded-full bg-gray-100 text-3xl font-semibold text-gray-600 ring-2 ring-white shadow-sm dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700"
            >
              {{ displayName.slice(0,1) }}
            </div>
          </div>

          <div class="min-w-0">
            <h1 class="truncate text-2xl font-bold text-gray-900 dark:text-gray-100">{{ displayName }}</h1>

            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
              <span v-if="student.cycle" class="inline-flex items-center gap-2 mr-3">
                <span class="text-xs rounded-full bg-indigo-100 px-2 py-0.5 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200">{{ student.cycle }}</span>
              </span>

              <span v-if="student.center" class="inline-block text-sm text-gray-500 dark:text-gray-400 mr-2">
                <svg class="inline h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                {{ student.center }}
              </span>

              <span v-if="student.city" class="inline-block text-sm text-gray-500 dark:text-gray-400">
                <svg class="inline h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-3-3h-2M2 20h5v-2a3 3 0 00-3-3H2m13-3V7a2 2 0 00-2-2H9a2 2 0 00-2 2v7"/></svg>
                {{ student.city }}
              </span>
            </p>
          </div>

          <div class="ml-auto hidden sm:flex sm:items-center sm:space-x-3">
            <span v-if="student.user_role" class="text-xs text-gray-500 dark:text-gray-400">Rol: {{ student.user_role }}</span>
          </div>
        </div>
      </section>

      <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Main column -->
        <div class="md:col-span-2 space-y-6">
          <!-- Documentos -->
          <section v-if="hasDocuments" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Documentos</h3>
              <span class="text-xs text-gray-400">Descargas</span>
            </div>

            <div class="mt-4 space-y-3">
              <div v-if="student.cv_url" class="flex items-center gap-3">
                <a :href="student.cv_url" target="_blank" class="text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400">Currículum (CV)</a>
              </div>

              <div v-if="student.cover_letter_url" class="flex items-center gap-3">
                <a :href="student.cover_letter_url" target="_blank" class="text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400">Carta de presentación</a>
              </div>

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
          <section v-if="hasInterests" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Intereses y perfil</h3>
              <span class="text-xs text-gray-400">Visión rápida</span>
            </div>

            <div class="mt-4 space-y-4">
              <div v-if="student.sectors?.length" class="flex flex-wrap gap-2">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 w-full">Sectores</div>
                <div class="flex flex-wrap gap-2">
                  <span v-for="(s,i) in student.sectors" :key="i" class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200">{{ s }}</span>
                </div>
              </div>

              <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div v-if="student.preferred_company_type">
                  <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Tipo de empresa</div>
                  <div class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ student.preferred_company_type }}</div>
                </div>

                <div v-if="student.non_formal_experience">
                  <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Experiencia no formal</div>
                  <div class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ student.non_formal_experience }}</div>
                </div>
              </div>

              <div v-if="student.tech_competencies?.length">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Competencias técnicas</div>
                <div class="mt-2 flex flex-wrap gap-2">
                  <span v-for="(t,i) in student.tech_competencies" :key="i" class="rounded-full bg-indigo-50 px-3 py-1 text-xs text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-200">{{ t }}</span>
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
          <section v-if="hasPersonal" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Datos personales</h3>
            <dl class="mt-3 grid grid-cols-1 gap-3 text-sm">
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
          <section v-if="hasAcademic" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Académicos</h3>
            <dl class="mt-3 grid grid-cols-1 gap-3 text-sm">
              <div v-if="student.cycle" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Ciclo</dt><dd>{{ student.cycle }}</dd></div>
              <div v-if="student.center" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Centro</dt><dd>{{ student.center }}</dd></div>
              <div v-if="student.year_start || student.year_end" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Años</dt>
                <dd>{{ student.year_start ? student.year_start : '—' }}<span v-if="student.year_start && student.year_end"> — </span>{{ student.year_end ? student.year_end : '' }}</dd>
              </div>
              <div v-if="student.fp_modality" class="flex justify-between text-gray-900 dark:text-gray-100"><dt class="text-gray-500 dark:text-gray-400">Modalidad FP</dt><dd class="capitalize">{{ student.fp_modality === 'dual' ? 'Dual' : 'General' }}</dd></div>
            </dl>
          </section>

          <!-- Disponibilidad (mejor espaciada, igual que Académicos) -->
          <section v-if="hasAvailability" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Disponibilidad</h3>
            <dl class="mt-3 grid grid-cols-1 gap-3 text-sm">
              <div v-if="student.availability_slot" class="flex justify-between text-gray-900 dark:text-gray-100">
                <dt class="text-gray-500 dark:text-gray-400">Franja</dt>
                <dd>{{ student.availability_slot === 'both' ? 'Ambas' : (student.availability_slot === 'morning' ? 'Mañana' : 'Tarde') }}</dd>
              </div>

              <div v-if="student.work_modality" class="flex justify-between text-gray-900 dark:text-gray-100">
                <dt class="text-gray-500 dark:text-gray-400">Modalidad</dt>
                <dd class="capitalize">{{ student.work_modality }}</dd>
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
          <section v-if="hasLinks" class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Enlaces</h3>
            <ul class="mt-3 space-y-2 text-sm">
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
  </AuthenticatedLayout>
</template>
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MunicipalitySearch from '@/Components/MunicipalitySearch.vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  student: { type: Object, required: true },
  allLanguages: { type: Array, default: () => [] },
})

const LEVELS = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2']
const levels = LEVELS

// ===== Tecnologías por categorías =====
const TECH_OPTIONS = [
  { category: 'Backend', items: ['.NET', 'C#', 'Django', 'Express', 'Flask', 'Go', 'Java', 'Laravel', 'Node.js', 'PHP', 'Python', 'Ruby on Rails', 'Rust', 'Spring Boot', 'Symfony'] },
  { category: 'Frontend', items: ['Angular', 'JavaScript', 'jQuery', 'Next.js', 'Nuxt', 'React', 'Svelte', 'TypeScript', 'Vue'] },
  { category: 'Bases de Datos', items: ['Elasticsearch', 'MariaDB', 'MongoDB', 'MySQL', 'PostgreSQL', 'Redis'] },
  { category: 'DevOps & Cloud', items: ['Ansible', 'Apache', 'AWS', 'Azure', 'CI/CD', 'Docker', 'GCP', 'GitHub Actions', 'GitLab CI', 'Kubernetes', 'Nginx', 'Terraform'] },
  { category: 'APIs & Mensajería', items: ['GraphQL', 'gRPC', 'Kafka', 'RabbitMQ', 'REST'] },
  { category: 'CSS & Build Tools', items: ['CSS', 'HTML', 'Less', 'Sass', 'Tailwind', 'Vite', 'Webpack'] },
  { category: 'Testing', items: ['Cypress', 'Jest', 'Pest', 'PHPUnit', 'Playwright', 'Vitest'] },
]

const isChecked = (list, value) =>
  Array.isArray(list) && list.includes(value)

const toggle = (list, value) => {
  if (!Array.isArray(list)) return
  const idx = list.indexOf(value)
  if (idx === -1) list.push(value)
  else list.splice(idx, 1)
}

// ===== Form principal =====
const form = useForm({
  // archivos
  avatar: null,
  cv: null,
  cover_letter: null,
  other_certs: [],

  // personales / contacto
  dni: props.student.dni ?? '',
  phone: props.student.phone ?? '',
  birth_date: props.student.birth_date ?? '',
  address: props.student.address ?? '',
  postal_code: props.student.postal_code ?? '',
  city: props.student.city ?? '',
  province: props.student.province ?? '',
  municipality: props.student.municipality ?? '',
  has_driver_license: props.student.has_driver_license ?? null,
  has_vehicle: props.student.has_vehicle ?? null,

  // académicos
  cycle: props.student.cycle ?? '',
  center: props.student.center ?? '',
  year_start: props.student.year_start ?? '',
  year_end: props.student.year_end ?? '',
  fp_modality: props.student.fp_modality ?? '',

  // disponibilidad
  availability_slot: props.student.availability_slot ?? '',
  commitments: Array.isArray(props.student.commitments) ? [...props.student.commitments] : [],
  relocate: props.student.relocate ?? null,
  relocate_cities: Array.isArray(props.student.relocate_cities) ? [...props.student.relocate_cities] : [],
  transport_own: props.student.transport_own ?? null,
  work_modality: props.student.work_modality ?? '',
  remote_days: props.student.remote_days ?? null,
  days_per_week: props.student.days_per_week ?? null,
  available_from: props.student.available_from ?? '',

  // intereses / perfil
  sectors: Array.isArray(props.student.sectors) ? [...props.student.sectors] : [],
  preferred_company_type: props.student.preferred_company_type ?? '',
  non_formal_experience: props.student.non_formal_experience ?? '',
  tech_competencies: Array.isArray(props.student.tech_competencies) ? [...props.student.tech_competencies] : [],
  soft_skills: Array.isArray(props.student.soft_skills) ? props.student.soft_skills.join(', ') : (props.student.soft_skills ?? ''),
  languages: Array.isArray(props.student.languages)
    ? props.student.languages.map(l => ({
        language_id: l.language_id ?? l.id ?? null,
        level: l.level ?? l.pivot?.nivel ?? 'A1',
      }))
    : [],
  certifications: Array.isArray(props.student.certifications) ? [...props.student.certifications] : [],

  // extra
  hobbies: props.student.hobbies ?? '',
  clubs: props.student.clubs ?? '',
  align_activities: props.student.align_activities ?? null,
  entrepreneurship: props.student.entrepreneurship ?? '',

  // links
  link_linkedin: props.student.link_linkedin ?? '',
  link_portfolio: props.student.link_portfolio ?? '',
  link_github: props.student.link_github ?? '',
  link_video: props.student.link_video ?? '',

  // auxiliares UI
  newCommitment: '',
  newRelocateCity: '',
  newSector: '',
  newSoft: '',
  newCert: '',
  newLanguageId: '',
  newLanguageLevel: '',
})

const previewUrl = ref(props.student.avatar_url ?? null)

// Helpers comunes
function emptyToNull(v) {
  if (v === undefined || v === null) return null
  if (typeof v === 'string' && v.trim() === '') return null
  return v
}
function numOrNull(v) {
  if (v === '' || v === null || v === undefined) return null
  const n = Number(v)
  return Number.isFinite(n) ? n : null
}
function boolOrNull(v) {
  if (v === true) return true
  if (v === false) return false
  return null
}

function addItem(field, valueField = null) {
  const val = (valueField ? form[valueField] : form[field])?.toString().trim()
  if (!val) return
  form[field].push(val)
  if (valueField) form[valueField] = ''
  else form[field] = ''
}
function removeItem(field, i) {
  form[field].splice(i, 1)
}

function addLanguage() {
  const langId = form.newLanguageId
  const lvl = form.newLanguageLevel || 'A1'
  if (!langId) return
  form.languages.push({ language_id: Number(langId), level: lvl })
  form.newLanguageId = ''
  form.newLanguageLevel = ''
}
function removeLanguage(i) {
  form.languages.splice(i, 1)
}

function handleMunicipalitySelect(municipality) {
  form.city = municipality.name
  form.province = municipality.province_name
}

function addCert() {
  const v = form.newCert?.toString().trim()
  if (!v) return
  form.certifications.push(v)
  form.newCert = ''
}
function removeCert(i) {
  form.certifications.splice(i, 1)
}

function onPickAvatar(e) {
  const f = e.target.files?.[0]
  form.avatar = f || null
  if (f) {
    const r = new FileReader()
    r.onload = () => (previewUrl.value = r.result)
    r.readAsDataURL(f)
  }
}
function onPickOtherCerts(e) {
  form.other_certs = Array.from(e.target.files || [])
}

const isHybrid = computed(() => form.work_modality === 'hibrida')

function submit() {
  form
    .transform((d) => ({
      // ficheros
      avatar: d.avatar,
      cv: d.cv,
      cover_letter: d.cover_letter,
      other_certs: d.other_certs,

      // personales
      dni: emptyToNull(d.dni),
      phone: emptyToNull(d.phone),
      birth_date: emptyToNull(d.birth_date),
      address: emptyToNull(d.address),
      postal_code: emptyToNull(d.postal_code),
      city: emptyToNull(d.city),
      province: emptyToNull(d.province),
      has_driver_license: boolOrNull(d.has_driver_license),
      has_vehicle: boolOrNull(d.has_vehicle),

      // académicos
      cycle: emptyToNull(d.cycle),
      center: emptyToNull(d.center),
      year_start: numOrNull(d.year_start),
      year_end: numOrNull(d.year_end),
      fp_modality: emptyToNull(d.fp_modality),

      // disponibilidad
      availability_slot: emptyToNull(d.availability_slot),
      commitments: JSON.stringify(d.commitments || []),
      relocate: boolOrNull(d.relocate),
      relocate_cities: JSON.stringify(d.relocate_cities || []),
      transport_own: boolOrNull(d.transport_own),
      work_modality: emptyToNull(d.work_modality),
      remote_days: isHybrid.value ? numOrNull(d.remote_days) : null,
      days_per_week: numOrNull(d.days_per_week),
      available_from: emptyToNull(d.available_from),

      // intereses / perfil
      sectors: JSON.stringify(d.sectors || []),
      preferred_company_type: emptyToNull(d.preferred_company_type),
      non_formal_experience: emptyToNull(d.non_formal_experience),
      tech_competencies: JSON.stringify(d.tech_competencies || []),
      soft_skills: JSON.stringify(d.soft_skills ? d.soft_skills.split(',').map(s => s.trim()).filter(Boolean) : []),
      competency_ids: d.competency_ids ?? [],
      languages: JSON.stringify(d.languages || []),
      certifications: JSON.stringify(d.certifications || []),

      // extra
      hobbies: emptyToNull(d.hobbies),
      clubs: emptyToNull(d.clubs),
      align_activities: boolOrNull(d.align_activities),
      entrepreneurship: emptyToNull(d.entrepreneurship),

      // links
      link_linkedin: emptyToNull(d.link_linkedin),
      link_portfolio: emptyToNull(d.link_portfolio),
      link_github: emptyToNull(d.link_github),
      link_video: emptyToNull(d.link_video),
    }))
    .post(route('students.update.me'), { forceFormData: true })
}

// ===== Formación (CRUD) =====
const showEduModal = ref(false)
const editingEdu = ref(null)
const eduForm = useForm({ title: '', center: '', start_date: '', end_date: '' })

function openEduCreate() {
  editingEdu.value = null
  eduForm.reset()
  eduForm.clearErrors()
  showEduModal.value = true
}
function openEduEdit(e) {
  editingEdu.value = e
  eduForm.reset()
  eduForm.clearErrors()
  eduForm.title = e.title || ''
  eduForm.center = e.center || ''
  eduForm.start_date = e.start_date || ''
  eduForm.end_date = e.end_date || ''
  showEduModal.value = true
}
function closeEdu() {
  showEduModal.value = false
  editingEdu.value = null
}

function saveEducation() {
  if (editingEdu.value) {
    eduForm.patch(route('students.educations.update', editingEdu.value.id), {
      preserveScroll: true,
      onSuccess: closeEdu,
    })
  } else {
    eduForm.post(route('students.educations.store'), {
      preserveScroll: true,
      onSuccess: closeEdu,
    })
  }
}
function deleteEducation(e) {
  if (!confirm('¿Eliminar esta formación?')) return
  router.delete(route('students.educations.destroy', e.id), { preserveScroll: true })
}

// ===== Experiencia (CRUD) =====
const showExpModal = ref(false)
const editingExp = ref(null)
const expForm = useForm({ company: '', position: '', start_date: '', end_date: '', functions: '', is_non_formal: false })

function openExpCreate() {
  editingExp.value = null
  expForm.reset()
  expForm.clearErrors()
  expForm.is_non_formal = false
  showExpModal.value = true
}
function openExpEdit(x) {
  editingExp.value = x
  expForm.reset()
  expForm.clearErrors()
  expForm.company = x.company || ''
  expForm.position = x.position || ''
  expForm.start_date = x.start_date || ''
  expForm.end_date = x.end_date || ''
  expForm.functions = x.functions || ''
  expForm.is_non_formal = !!x.is_non_formal
  showExpModal.value = true
}
function closeExp() {
  showExpModal.value = false
  editingExp.value = null
}

function saveExperience() {
  if (editingExp.value) {
    expForm.patch(route('students.experiences.update', editingExp.value.id), {
      preserveScroll: true,
      onSuccess: closeExp,
    })
  } else {
    expForm.post(route('students.experiences.store'), {
      preserveScroll: true,
      onSuccess: closeExp,
    })
  }
}
function deleteExperience(x) {
  if (!confirm('¿Eliminar esta experiencia?')) return
  router.delete(route('students.experiences.destroy', x.id), { preserveScroll: true })
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Header con gradiente -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-100 bg-gradient-to-br from-indigo-600 to-purple-600 p-8 shadow-xl dark:border-gray-800 dark:from-indigo-700 dark:to-purple-700">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-white">Mi Perfil</h1>
              <p class="mt-2 text-indigo-100">Completa tu información para destacar ante las empresas</p>
            </div>
            <Link
              v-if="props.student.id"
              :href="route('students.public.show', props.student.id)"
              class="inline-flex items-center gap-2 rounded-xl border-2 border-white/30 bg-white/20 px-5 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:bg-white/30"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              Ver perfil público
            </Link>
          </div>
        </div>

        <div class="space-y-6">
          <!-- Avatar & documentos -->
          <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
              <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Imagen y documentos
              </h3>
            </div>
            <div class="p-6">
              <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <div class="flex items-center gap-4">
            <img
              v-if="previewUrl"
              :src="previewUrl"
              alt="avatar"
              class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
            />
            <div class="flex flex-col gap-2">
              <input type="file" accept="image/*" @change="onPickAvatar" class="text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/50" />
              <p class="text-xs text-gray-500 dark:text-gray-400">PNG/JPG, máx. 2 MB.</p>
            </div>
          </div>
          <div class="grid grid-cols-1 gap-3">
            <div>
              <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">CV (PDF/DOC)</label>
              <input
                type="file"
                accept=".pdf,.doc,.docx"
                @change="e => (form.cv = e.target.files?.[0] || null)"
                class="text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/50"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Carta de presentación (opcional)</label>
              <input
                type="file"
                accept=".pdf,.doc,.docx"
                @change="e => (form.cover_letter = e.target.files?.[0] || null)"
                class="text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/50"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Otros certificados (múltiples)</label>
              <input
                type="file"
                multiple
                accept=".pdf,.jpg,.jpeg,.png"
                @change="onPickOtherCerts"
                class="text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/50"
              />
            </div>
          </div>
        </div>
        <div v-if="form.errors.avatar || form.errors.cv || form.errors.cover_letter || form.errors['other_certs.*']" 
             class="mt-4 rounded-lg bg-red-50 p-3 text-sm text-red-600 dark:bg-red-900/20 dark:text-red-400">
          {{
            form.errors.avatar ||
            form.errors.cv ||
            form.errors.cover_letter ||
            form.errors['other_certs.*']
          }}
        </div>
      </div>
      </section>

      <!-- Datos personales -->
      <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
          <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Datos personales
          </h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">DNI/NIE</label>
            <input
              v-model="form.dni"
              type="text"
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.dni" class="mt-1 text-xs text-red-600 dark:text-red-400">{{ form.errors.dni }}</div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Teléfono</label>
            <input
              v-model="form.phone"
              type="text"
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.phone" class="mt-1 text-xs text-red-600 dark:text-red-400">{{ form.errors.phone }}</div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de nacimiento</label>
            <input
              v-model="form.birth_date"
              type="date"
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.birth_date" class="mt-1 text-xs text-red-600 dark:text-red-400">
              {{ form.errors.birth_date }}
            </div>
          </div>
          <div class="sm:col-span-2">
            <MunicipalitySearch
              label="Municipio y Provincia"
              placeholder="Escribe para buscar tu municipio..."
              @select="handleMunicipalitySelect"
              :error="form.errors.city || form.errors.province"
            />
            <div v-if="form.city && form.province" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
              Seleccionado: <strong>{{ form.city }}</strong> ({{ form.province }})
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Dirección</label>
            <input
              v-model="form.address"
              type="text"
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Código postal</label>
            <input
              v-model="form.postal_code"
              type="text"
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div class="flex items-center gap-6 sm:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
              <input
                v-model="form.has_driver_license"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700"
              />
              Carnet de conducir
            </label>
            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
              <input
                v-model="form.has_vehicle"
                type="checkbox"
                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700"
              />
              Vehículo propio
            </label>
          </div>
        </div>
      </div>
      </section>

      <!-- Académicos -->
      <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
          <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Académicos
          </h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Ciclo</label>
            <select
              v-model="form.cycle"
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            >
              <option value="">— Selecciona —</option>
              <option value="1 DAM">1 DAM</option>
              <option value="2 DAM">2 DAM</option>
              <option value="1 DAW">1 DAW</option>
              <option value="2 DAW">2 DAW</option>
            </select>
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Centro</label>
            <input
              v-model="form.center"
              type="text"
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Año inicio</label>
            <input
              v-model="form.year_start"
              type="number"
              min="1990"
              max="2100"
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Año fin</label>
            <input
              v-model="form.year_end"
              type="number"
              min="1990"
              max="2100"
              class="w-full rounded-xl border border-gray-300 px-4 py-2.5 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Modalidad FP</label>
            <div class="flex items-center gap-4">
              <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                <input v-model="form.fp_modality" type="radio" value="dual" class="h-4 w-4 text-indigo-600 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800" />
                Dual
              </label>
              <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                <input v-model="form.fp_modality" type="radio" value="general" class="h-4 w-4 text-indigo-600 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800" />
                General
              </label>
            </div>
          </div>
        </div>
      </div>
      </section>

      <!-- Disponibilidad -->
      <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
          <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Disponibilidad
          </h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Franja</label>
            <select v-model="form.availability_slot"
                    required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
              <option value="">— Selecciona —</option>
              <option value="manana">Mañana</option>
              <option value="tarde">Tarde</option>
              <option value="completa">Completa</option>
            </select>
            <div v-if="form.errors.availability_slot" class="text-xs text-red-600">{{ form.errors.availability_slot }}</div>
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Modalidad</label>
            <select
              v-model="form.work_modality"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            >
              <option value="">— Selecciona —</option>
              <option value="presencial">Presencial</option>
              <option value="remota">Remota</option>
              <option value="hibrida">Híbrida</option>
              <option value="indiferente">Indiferente</option>
            </select>
          </div>
          <div v-if="form.work_modality === 'hibrida'">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Días en remoto</label>
            <input
              v-model="form.remote_days"
              type="number"
              min="0"
              max="7"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Días/semana</label>
            <input
              v-model="form.days_per_week"
              type="number"
              min="1"
              max="7"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Fecha inicio disponible</label>
            <input
              v-model="form.available_from"
              type="date"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div class="flex items-center gap-6 sm:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input v-model="form.transport_own" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800" />
              Transporte propio
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input v-model="form.relocate" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800" />
              Puedo desplazarme
            </label>
          </div>
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">
              Ciudades a las que puedes desplazarte
            </label>
            <div class="flex flex-wrap gap-2">
              <input
                v-model="form.newRelocateCity"
                type="text"
                placeholder="Ciudad"
                class="w-48 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                @keyup.enter="addItem('relocate_cities', 'newRelocateCity')"
              />
              <button
                type="button"
                @click="addItem('relocate_cities', 'newRelocateCity')"
                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 cursor-pointer"
              >
                Añadir
              </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(c, i) in form.relocate_cities"
                :key="i"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300"
              >
                {{ c }}
                <button class="ms-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 cursor-pointer" @click="removeItem('relocate_cities', i)">✕</button>
              </span>
            </div>
          </div>
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">
              Compromisos que afecten tu disponibilidad
            </label>
            <div class="flex flex-wrap gap-2">
              <input
                v-model="form.newCommitment"
                type="text"
                placeholder="Ej. Trabajo parcial"
                class="w-64 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                @keyup.enter="addItem('commitments', 'newCommitment')"
              />
              <button
                type="button"
                @click="addItem('commitments', 'newCommitment')"
                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 cursor-pointer"
              >
                Añadir
              </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(c, i) in form.commitments"
                :key="i"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300"
              >
                {{ c }}
                <button class="ms-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 cursor-pointer" @click="removeItem('commitments', i)">✕</button>
              </span>
            </div>
          </div>
        </div>
      </div>
      </section>

      <!-- Intereses y perfil -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Intereses y perfil</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Sectores de interés</label>
            <div class="flex flex-wrap gap-2">
              <input
                v-model="form.newSector"
                type="text"
                placeholder="Ej. Desarrollo web"
                class="w-64 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                @keyup.enter="addItem('sectors', 'newSector')"
              />
              <button
                type="button"
                @click="addItem('sectors', 'newSector')"
                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 cursor-pointer"
              >
                Añadir
              </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(s, i) in form.sectors"
                :key="i"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300"
              >
                {{ s }}
                <button class="ms-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 cursor-pointer" @click="removeItem('sectors', i)">✕</button>
              </span>
            </div>
          </div>

          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Presentación</label>
            <textarea
              v-model="form.non_formal_experience"
              rows="4"
              placeholder="Cuéntanos sobre ti, tus proyectos personales, logros destacados, cuál es tu principal motivación para realizar prácticas en empresa, tienes alguna limitación que debamos conocer o indica cualquier otra información que consideres relevante para la empresa..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Este texto aparecerá en tu perfil público y CV</p>
          </div>
          
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Tipos de empresas de interés</label>
            <input
              v-model="form.preferred_company_type"
              type="text"
              placeholder="Ej: startup, multinacional, pyme, consultoría..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>


          <!-- Tecnologías por categorías -->
          <div class="sm:col-span-2">
            <label class="mb-2 block text-sm text-gray-600 dark:text-gray-300">Tecnologías</label>
            <div v-for="group in TECH_OPTIONS" :key="group.category" class="mb-3">
              <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ group.category }}</h4>
              <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
                <label
                  v-for="t in group.items"
                  :key="t"
                  class="inline-flex items-center gap-2 text-xs text-gray-700 dark:text-gray-300"
                >
                  <input
                    type="checkbox"
                    :value="t"
                    :checked="isChecked(form.tech_competencies, t)"
                    @change="toggle(form.tech_competencies, t)"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800"
                  />
                  <span>{{ t }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Soft skills -->
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">
              Habilidades personales
            </label>
            <input
              v-model="form.soft_skills"
              type="text"
              placeholder="Ej: Trabajo en equipo, Comunicación, Proactividad, Resolución de problemas"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Separa las habilidades con comas (,)</p>
          </div>

          <!-- Idiomas -->
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Idiomas</label>
            <div class="flex flex-wrap items-center gap-2">
              <select
                v-model="form.newLanguageId"
                class="w-40 rounded-lg border border-gray-300 px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
              >
                <option value="">Idioma…</option>
                <option
                  v-for="lang in props.allLanguages"
                  :key="lang.id"
                  :value="lang.id"
                >
                  {{ lang.name }}
                </option>
              </select>
              <select
                v-model="form.newLanguageLevel"
                class="w-32 rounded-lg border border-gray-300 px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
              >
                <option value="">Nivel…</option>
                <option v-for="lvl in levels" :key="lvl" :value="lvl">
                  {{ lvl }}
                </option>
              </select>
              <button
                type="button"
                @click="addLanguage"
                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 cursor-pointer"
              >
                Añadir
              </button>
            </div>
            <ul class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
              <li
                v-for="(lng, i) in form.languages"
                :key="i"
                class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 text-sm dark:bg-gray-800/60"
              >
                <span class="truncate">
                  <strong>
                    {{
                      (props.allLanguages.find(l => l.id === lng.language_id)?.name) ||
                      'Idioma'
                    }}
                  </strong>
                  <span v-if="lng.level"> · {{ lng.level }}</span>
                </span>
                <button
                  type="button"
                  @click="removeLanguage(i)"
                  class="text-xs text-gray-500 hover:text-red-600"
                >
                  Eliminar
                </button>
              </li>
            </ul>
          </div>

          <!-- Certificaciones -->
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Certificaciones</label>
            <div class="flex flex-wrap gap-2">
              <input
                v-model="form.newCert"
                type="text"
                placeholder="Ej. Cisco CCNA"
                class="w-56 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                @keyup.enter="addCert"
              />
              <button
                type="button"
                @click="addCert"
                class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 cursor-pointer"
              >
                Añadir
              </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(c, i) in form.certifications"
                :key="i"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300"
              >
                {{ c }}
                <button class="ms-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 cursor-pointer" @click="removeCert(i)">✕</button>
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- Formación (CRUD) -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="mb-3 flex items-center justify-between">
          <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Formación</h3>
          <button
            type="button"
            @click="openEduCreate"
            class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 cursor-pointer"
          >
            Añadir
          </button>
        </div>
        <div v-if="props.student.educations?.length" class="space-y-2">
          <div
            v-for="e in props.student.educations"
            :key="e.id"
            class="flex items-center justify-between rounded-lg bg-gray-50 p-3 text-sm dark:bg-gray-800/60"
          >
            <div>
              <div class="font-medium text-gray-800 dark:text-gray-100">{{ e.title }}</div>
              <div class="text-gray-500 dark:text-gray-400">
                <span v-if="e.center">{{ e.center }} · </span>
                {{ e.start_date || '—' }} — {{ e.end_date || 'Actual' }}
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button
                type="button"
                @click="openEduEdit(e)"
                class="rounded-md px-2 py-1 text-xs text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-900/20"
              >
                Editar
              </button>
              <button
                type="button"
                @click="deleteEducation(e)"
                class="rounded-md px-2 py-1 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
              >
                Eliminar
              </button>
            </div>
          </div>
        </div>
        <p v-else class="text-sm text-gray-500 dark:text-gray-400">Aún no has añadido formación.</p>
      </section>

      <!-- Experiencia (CRUD) -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <div class="mb-3 flex items-center justify-between">
          <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Experiencia</h3>
          <button
            type="button"
            @click="openExpCreate"
            class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 cursor-pointer"
          >
            Añadir
          </button>
        </div>
        <div v-if="props.student.experiences?.length" class="space-y-2">
          <div
            v-for="x in props.student.experiences"
            :key="x.id"
            class="flex items-center justify-between rounded-lg bg-gray-50 p-3 text-sm dark:bg-gray-800/60"
          >
            <div>
              <div class="font-medium text-gray-800 dark:text-gray-100">
                {{ x.company }}
                <span v-if="x.position" class="text-gray-500 dark:text-gray-400">· {{ x.position }}</span>
              </div>
              <div class="text-gray-500 dark:text-gray-400">
                {{ x.start_date || '—' }} — {{ x.end_date || 'Actual' }}
                <span
                  v-if="x.is_non_formal"
                  class="ms-2 rounded bg-gray-200 px-2 py-0.5 text-[11px] dark:bg-gray-700"
                >
                  No formal
                </span>
              </div>
              <div v-if="x.functions" class="text-gray-700 dark:text-gray-300">
                {{ x.functions }}
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button
                type="button"
                @click="openExpEdit(x)"
                class="rounded-md px-2 py-1 text-xs text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-900/20"
              >
                Editar
              </button>
              <button
                type="button"
                @click="deleteExperience(x)"
                class="rounded-md px-2 py-1 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
              >
                Eliminar
              </button>
            </div>
          </div>
        </div>
        <p v-else class="text-sm text-gray-500 dark:text-gray-400">Aún no has añadido experiencia.</p>
      </section>

      <!-- Enlaces -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Enlaces</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">LinkedIn</label>
            <input
              v-model="form.link_linkedin"
              type="url"
              placeholder="https://..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Portfolio</label>
            <input
              v-model="form.link_portfolio"
              type="url"
              placeholder="https://..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">GitHub</label>
            <input
              v-model="form.link_github"
              type="url"
              placeholder="https://..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Vídeo presentación</label>
            <input
              v-model="form.link_video"
              type="url"
              placeholder="https://..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
        </div>
      </section>

      <!-- Guardar perfil -->
      <div class="flex items-center justify-end gap-4">
        <span v-if="$page.props.flash?.success" class="text-sm font-medium text-emerald-700 dark:text-emerald-400">
          ✓ {{ $page.props.flash.success }}
        </span>
        <button
          type="submit"
          @click="submit"
          :disabled="form.processing"
          class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:scale-100 dark:from-indigo-700 dark:to-purple-700"
        >
          <svg v-if="!form.processing" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <svg v-else class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
        </button>
      </div>
      </div>
    </div>
    </div>

    <!-- ===== MODAL Formación ===== -->
    <div v-if="showEduModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="closeEdu"></div>
      <div
        class="relative w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-5 shadow-xl dark:border-gray-800 dark:bg-gray-900"
      >
        <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-gray-100">
          {{ editingEdu ? 'Editar formación' : 'Añadir formación' }}
        </h3>
        <div class="grid grid-cols-1 gap-3">
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Título</label>
            <input
              v-model="eduForm.title"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="eduForm.errors.title" class="mt-1 text-xs text-red-600">
              {{ eduForm.errors.title }}
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Centro</label>
            <input
              v-model="eduForm.center"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Inicio</label>
              <input
                v-model="eduForm.start_date"
                type="date"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Fin</label>
              <input
                v-model="eduForm.end_date"
                type="date"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
              />
            </div>
          </div>
        </div>
        <div class="mt-5 flex items-center justify-end gap-2">
          <button
            type="button"
            @click="closeEdu"
            class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="saveEducation"
            :disabled="eduForm.processing"
            class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white disabled:opacity-50 dark:bg-indigo-500"
          >
            Guardar
          </button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL Experiencia ===== -->
    <div v-if="showExpModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="closeExp"></div>
      <div
        class="relative w-full max-w-xl rounded-2xl border border-gray-200 bg-white p-5 shadow-xl dark:border-gray-800 dark:bg-gray-900"
      >
        <h3 class="mb-4 text-base font-semibold text-gray-900 dark:text-gray-100">
          {{ editingExp ? 'Editar experiencia' : 'Añadir experiencia' }}
        </h3>
        <div class="grid grid-cols-1 gap-3">
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Empresa</label>
            <input
              v-model="expForm.company"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="expForm.errors.company" class="mt-1 text-xs text-red-600">
              {{ expForm.errors.company }}
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Puesto (opcional)</label>
            <input
              v-model="expForm.position"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
              <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Inicio</label>
              <input
                v-model="expForm.start_date"
                type="date"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
              />
            </div>
            <div>
              <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Fin</label>
              <input
                v-model="expForm.end_date"
                type="date"
                class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
              />
            </div>
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Funciones / tareas</label>
            <textarea
              v-model="expForm.functions"
              rows="3"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            ></textarea>
          </div>
          <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
            <input
              v-model="expForm.is_non_formal"
              type="checkbox"
              class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800"
            />
            Experiencia no formal
          </label>
        </div>
        <div class="mt-5 flex items-center justify-end gap-2">
          <button
            type="button"
            @click="closeExp"
            class="rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
          >
            Cancelar
          </button>
          <button
            type="button"
            @click="saveExperience"
            :disabled="expForm.processing"
            class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white disabled:opacity-50 dark:bg-indigo-500"
          >
            Guardar
          </button>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
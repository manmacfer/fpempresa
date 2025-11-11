<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  student: { type: Object, required: true },
})

/**
 * FORM: inicializa con lo que venga de props.student
 * Los arrays vienen como [] si no hay datos
 */
const form = useForm({
  // AVATAR
  avatar: null,

  // PERSONALES / CONTACTO
  phone: props.student.phone ?? '',
  dni: props.student.dni ?? '',
  birth_date: props.student.birth_date ?? '', // "YYYY-MM-DD"
  address: props.student.address ?? '',
  postal_code: props.student.postal_code ?? '',
  city: props.student.city ?? '',
  has_driver_license: !!props.student.has_driver_license,
  has_vehicle: !!props.student.has_vehicle,

  // ACADÉMICOS (ciclo actual)
  cycle: props.student.cycle ?? '',
  center: props.student.center ?? '',
  year_start: props.student.year_start ?? '',
  year_end: props.student.year_end ?? '',
  fp_modality: props.student.fp_modality ?? '', // 'dual' | 'general'

  // DISPONIBILIDAD
  availability_slot: props.student.availability_slot ?? '', // 'morning' | 'afternoon' | 'both'
  commitments: Array.isArray(props.student.commitments) ? [...props.student.commitments] : [],
  relocate: props.student.relocate ?? null, // true/false/null
  relocate_cities: Array.isArray(props.student.relocate_cities) ? [...props.student.relocate_cities] : [],
  transport_own: props.student.transport_own ?? null, // true/false/null
  work_modality: props.student.work_modality ?? '', // 'presencial' | 'remota' | 'hibrida'
  remote_days: props.student.remote_days ?? null, // 0..7 si hibrida
  days_per_week: props.student.days_per_week ?? null, // 1..7
  available_from: props.student.available_from ?? '',

  // INTERESES / COMPETENCIAS / IDIOMAS
  sectors: Array.isArray(props.student.sectors) ? [...props.student.sectors] : [],
  preferred_company_type: props.student.preferred_company_type ?? '',
  non_formal_experience: props.student.non_formal_experience ?? '',

  tech_competencies: Array.isArray(props.student.tech_competencies) ? [...props.student.tech_competencies] : [],
  soft_skills: Array.isArray(props.student.soft_skills) ? [...props.student.soft_skills] : [],
  languages: Array.isArray(props.student.languages) ? [...props.student.languages] : [], // [{name,level}]
  certifications: Array.isArray(props.student.certifications) ? [...props.student.certifications] : [],

  // EXTRA
  hobbies: props.student.hobbies ?? '',
  clubs: props.student.clubs ?? '',
  motivation: props.student.motivation ?? '',
  limitations: props.student.limitations ?? '',

  // ENLACES
  link_linkedin: props.student.link_linkedin ?? '',
  link_portfolio: props.student.link_portfolio ?? '',
  link_github: props.student.link_github ?? '',
  link_video: props.student.link_video ?? '',

  // AUX inputs para añadir items
  newCommitment: '',
  newRelocateCity: '',
  newSector: '',
  newTech: '',
  newSoft: '',
  newCert: '',
  newLangName: '',
  newLangLevel: '',
})

const previewUrl = ref(props.student.avatar_url ?? null)

// Helpers “añadir/quitar” en arrays simples
function addItem(field, valueField = null) {
  const val = (valueField ? form[valueField] : form[field])?.toString().trim()
  if (!val) return
  form[field].push(val)
  if (valueField) form[valueField] = ''
  else form[field] = ''
}
function removeItem(field, idx) {
  form[field].splice(idx, 1)
}

// Idiomas (array de objetos)
function addLanguage() {
  const name = form.newLangName?.toString().trim()
  const level = form.newLangLevel?.toString().trim()
  if (!name) return
  form.languages.push({ name, level })
  form.newLangName = ''
  form.newLangLevel = ''
}
function removeLanguage(i) {
  form.languages.splice(i, 1)
}

// Avatar
function onPickAvatar(e) {
  const f = e.target.files?.[0]
  form.avatar = f || null
  if (f) {
    const r = new FileReader()
    r.onload = () => (previewUrl.value = r.result)
    r.readAsDataURL(f)
  }
}

// Deshabilitar remote_days si NO es hibrida
const isHybrid = computed(() => form.work_modality === 'hibrida')

// Envío
function submit() {
  form
    .transform((d) => ({
      // AVATAR
      avatar: d.avatar,

      // PERSONALES
      phone: emptyToNull(d.phone),
      dni: emptyToNull(d.dni),
      birth_date: emptyToNull(d.birth_date),
      address: emptyToNull(d.address),
      postal_code: emptyToNull(d.postal_code),
      city: emptyToNull(d.city),
      has_driver_license: boolOrNull(d.has_driver_license),
      has_vehicle: boolOrNull(d.has_vehicle),

      // ACADÉMICOS
      cycle: emptyToNull(d.cycle),
      center: emptyToNull(d.center),
      year_start: numOrNull(d.year_start),
      year_end: numOrNull(d.year_end),
      fp_modality: emptyToNull(d.fp_modality),

      // DISPONIBILIDAD
      availability_slot: emptyToNull(d.availability_slot),
      commitments: d.commitments,
      relocate: boolOrNull(d.relocate),
      relocate_cities: d.relocate_cities,
      transport_own: boolOrNull(d.transport_own),
      work_modality: emptyToNull(d.work_modality),
      remote_days: isHybrid.value ? numOrNull(d.remote_days) : null,
      days_per_week: numOrNull(d.days_per_week),
      available_from: emptyToNull(d.available_from),

      // INTERESES / COMPETENCIAS / IDIOMAS
      sectors: d.sectors,
      preferred_company_type: emptyToNull(d.preferred_company_type),
      non_formal_experience: emptyToNull(d.non_formal_experience),
      tech_competencies: d.tech_competencies,
      soft_skills: d.soft_skills,
      languages: d.languages,
      certifications: d.certifications,

      // EXTRA
      hobbies: emptyToNull(d.hobbies),
      clubs: emptyToNull(d.clubs),
      motivation: emptyToNull(d.motivation),
      limitations: emptyToNull(d.limitations),

      // ENLACES
      link_linkedin: emptyToNull(d.link_linkedin),
      link_portfolio: emptyToNull(d.link_portfolio),
      link_github: emptyToNull(d.link_github),
      link_video: emptyToNull(d.link_video),
    }))
    .patch(route('students.update.me'), { forceFormData: true })
}

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
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Mi perfil</h2>
        <Link v-if="route().has && route().has('students.public.show') && props.student.id"
              :href="route('students.public.show', props.student.id)"
              class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
          Ver perfil público
        </Link>
      </div>
    </template>

    <div class="mx-auto max-w-5xl p-6 space-y-8">
      <!-- Avatar -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Avatar</h3>
        <div class="flex items-center gap-4">
          <img
            v-if="previewUrl"
            :src="previewUrl"
            alt="avatar"
            class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700"
          />
          <div class="flex flex-col gap-2">
            <input type="file" accept="image/*" @change="onPickAvatar" />
            <p class="text-xs text-gray-500 dark:text-gray-400">PNG/JPG, máx. 2 MB.</p>
          </div>
        </div>
        <div v-if="form.errors.avatar" class="mt-2 text-xs text-red-600">{{ form.errors.avatar }}</div>
      </section>

      <!-- Datos personales y contacto -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Datos personales y contacto</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Teléfono</label>
            <input v-model="form.phone" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">DNI</label>
            <input v-model="form.dni" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.dni" class="mt-1 text-xs text-red-600">{{ form.errors.dni }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Fecha de nacimiento</label>
            <input v-model="form.birth_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.birth_date" class="mt-1 text-xs text-red-600">{{ form.errors.birth_date }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Ciudad</label>
            <input v-model="form.city" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.city" class="mt-1 text-xs text-red-600">{{ form.errors.city }}</div>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Dirección</label>
            <input v-model="form.address" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.address" class="mt-1 text-xs text-red-600">{{ form.errors.address }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Código postal</label>
            <input v-model="form.postal_code" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.postal_code" class="mt-1 text-xs text-red-600">{{ form.errors.postal_code }}</div>
          </div>
          <div class="flex items-center gap-6">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input v-model="form.has_driver_license" type="checkbox" class="rounded border-gray-300 dark:border-gray-700" />
              Carnet de conducir
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input v-model="form.has_vehicle" type="checkbox" class="rounded border-gray-300 dark:border-gray-700" />
              Vehículo propio
            </label>
          </div>
        </div>
      </section>

      <!-- Formación (ciclo actual) -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Formación (ciclo actual)</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Ciclo</label>
            <input v-model="form.cycle" type="text" placeholder="CFGS DAW, etc." class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.cycle" class="mt-1 text-xs text-red-600">{{ form.errors.cycle }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Centro</label>
            <input v-model="form.center" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.center" class="mt-1 text-xs text-red-600">{{ form.errors.center }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Año inicio</label>
            <input v-model="form.year_start" type="number" min="1990" max="2100" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.year_start" class="mt-1 text-xs text-red-600">{{ form.errors.year_start }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Año fin</label>
            <input v-model="form.year_end" type="number" min="1990" max="2100" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.year_end" class="mt-1 text-xs text-red-600">{{ form.errors.year_end }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Modalidad FP</label>
            <select v-model="form.fp_modality" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
              <option value="">—</option>
              <option value="dual">Dual</option>
              <option value="general">General</option>
            </select>
            <div v-if="form.errors.fp_modality" class="mt-1 text-xs text-red-600">{{ form.errors.fp_modality }}</div>
          </div>
        </div>
      </section>

      <!-- Disponibilidad -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Disponibilidad</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Horario disponible</label>
            <select v-model="form.availability_slot" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
              <option value="">—</option>
              <option value="morning">Mañana</option>
              <option value="afternoon">Tarde</option>
              <option value="both">Ambos</option>
            </select>
            <div v-if="form.errors.availability_slot" class="mt-1 text-xs text-red-600">{{ form.errors.availability_slot }}</div>
          </div>

          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Modalidad de trabajo</label>
            <select v-model="form.work_modality" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
              <option value="">—</option>
              <option value="presencial">Presencial</option>
              <option value="remota">Remota</option>
              <option value="hibrida">Híbrida</option>
            </select>
            <div v-if="form.errors.work_modality" class="mt-1 text-xs text-red-600">{{ form.errors.work_modality }}</div>
          </div>

          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Días remotos (si híbrida)</label>
            <input :disabled="!isHybrid" v-model="form.remote_days" type="number" min="0" max="7" class="w-full rounded-lg border border-gray-300 px-3 py-2 disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.remote_days" class="mt-1 text-xs text-red-600">{{ form.errors.remote_days }}</div>
          </div>

          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Días/semana</label>
            <input v-model="form.days_per_week" type="number" min="1" max="7" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.days_per_week" class="mt-1 text-xs text-red-600">{{ form.errors.days_per_week }}</div>
          </div>

          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Disponible desde</label>
            <input v-model="form.available_from" type="date" class="w-full sm:w-60 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.available_from" class="mt-1 text-xs text-red-600">{{ form.errors.available_from }}</div>
          </div>

          <div class="flex items-center gap-6">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input v-model="form.relocate" :true-value="true" :false-value="false" type="checkbox" class="rounded border-gray-300 dark:border-gray-700" />
              Dispuesto a trasladarse
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input v-model="form.transport_own" :true-value="true" :false-value="false" type="checkbox" class="rounded border-gray-300 dark:border-gray-700" />
              Transporte propio
            </label>
          </div>

          <!-- Compromisos -->
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Compromisos (lista)</label>
            <div class="flex gap-2">
              <input v-model="form.newCommitment" type="text" placeholder="Trabajo parcial, cuidado de dependientes…" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
              <button type="button" @click="addItem('commitments','newCommitment')" class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900">Añadir</button>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
              <span v-for="(c,i) in form.commitments" :key="i" class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800">
                {{ c }} <button type="button" @click="removeItem('commitments', i)" class="text-gray-500 hover:text-red-600">×</button>
              </span>
            </div>
            <div v-if="form.errors['commitments']" class="mt-1 text-xs text-red-600">{{ form.errors['commitments'] }}</div>
          </div>

          <!-- Ciudades a las que se trasladaría -->
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Ciudades a las que me trasladaría</label>
            <div class="flex gap-2">
              <input v-model="form.newRelocateCity" type="text" placeholder="Sevilla, Madrid, Málaga…" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
              <button type="button" @click="addItem('relocate_cities','newRelocateCity')" class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900">Añadir</button>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
              <span v-for="(rc,i) in form.relocate_cities" :key="i" class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800">
                {{ rc }} <button type="button" @click="removeItem('relocate_cities', i)" class="text-gray-500 hover:text-red-600">×</button>
              </span>
            </div>
            <div v-if="form.errors['relocate_cities']" class="mt-1 text-xs text-red-600">{{ form.errors['relocate_cities'] }}</div>
          </div>
        </div>
      </section>

      <!-- Intereses / preferencias -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Intereses / preferencias</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Sectores de interés (lista)</label>
            <div class="flex gap-2">
              <input v-model="form.newSector" type="text" placeholder="Desarrollo Web, Ciberseguridad…" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
              <button type="button" @click="addItem('sectors','newSector')" class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900">Añadir</button>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
              <span v-for="(s,i) in form.sectors" :key="i" class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800">
                {{ s }} <button type="button" @click="removeItem('sectors', i)" class="text-gray-500 hover:text-red-600">×</button>
              </span>
            </div>
            <div v-if="form.errors['sectors']" class="mt-1 text-xs text-red-600">{{ form.errors['sectors'] }}</div>
          </div>

          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Tipo de empresa preferida</label>
            <input v-model="form.preferred_company_type" type="text" placeholder="Startup, consultora, producto…" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.preferred_company_type" class="mt-1 text-xs text-red-600">{{ form.errors.preferred_company_type }}</div>
          </div>

          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Experiencia no formal</label>
            <textarea v-model="form.non_formal_experience" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
            <div v-if="form.errors.non_formal_experience" class="mt-1 text-xs text-red-600">{{ form.errors.non_formal_experience }}</div>
          </div>
        </div>
      </section>

      <!-- Competencias -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Competencias</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- Tech -->
          <div>
            <h4 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Técnicas</h4>
            <div class="flex gap-2">
              <input v-model="form.newTech" type="text" placeholder="Laravel, Vue, SQL…" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
              <button type="button" @click="addItem('tech_competencies','newTech')" class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900">Añadir</button>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
              <span v-for="(sk,i) in form.tech_competencies" :key="i" class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800">
                {{ sk }} <button type="button" @click="removeItem('tech_competencies', i)" class="text-gray-500 hover:text-red-600">×</button>
              </span>
            </div>
            <div v-if="form.errors['tech_competencies']" class="mt-1 text-xs text-red-600">{{ form.errors['tech_competencies'] }}</div>
          </div>

          <!-- Soft -->
          <div>
            <h4 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Soft skills</h4>
            <div class="flex gap-2">
              <input v-model="form.newSoft" type="text" placeholder="Trabajo en equipo, Comunicación…" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
              <button type="button" @click="addItem('soft_skills','newSoft')" class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900">Añadir</button>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
              <span v-for="(ss,i) in form.soft_skills" :key="i" class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800">
                {{ ss }} <button type="button" @click="removeItem('soft_skills', i)" class="text-gray-500 hover:text-red-600">×</button>
              </span>
            </div>
            <div v-if="form.errors['soft_skills']" class="mt-1 text-xs text-red-600">{{ form.errors['soft_skills'] }}</div>
          </div>
        </div>
      </section>

      <!-- Idiomas -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Idiomas</h3>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end">
          <div class="flex-1">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Idioma</label>
            <input v-model="form.newLangName" type="text" placeholder="Inglés, Francés…" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
          </div>
          <div class="flex-1">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Nivel</label>
            <input v-model="form.newLangLevel" type="text" placeholder="A2, B1, B2, C1…" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
          </div>
          <button type="button" @click="addLanguage" class="h-10 rounded-lg bg-gray-900 px-3 text-sm text-white dark:bg-gray-100 dark:text-gray-900">
            Añadir
          </button>
        </div>

        <ul class="mt-3 space-y-2">
          <li v-for="(lng,i) in form.languages" :key="i" class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 dark:bg-gray-800/60">
            <span class="text-sm">
              <strong>{{ lng.name }}</strong>
              <span v-if="lng.level" class="text-gray-500"> · {{ lng.level }}</span>
            </span>
            <button type="button" @click="removeLanguage(i)" class="text-xs text-gray-500 hover:text-red-600">Quitar</button>
          </li>
        </ul>
        <div v-if="form.errors['languages']" class="mt-1 text-xs text-red-600">{{ form.errors['languages'] }}</div>
      </section>

      <!-- Certificaciones -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Certificaciones</h3>
        <div class="flex gap-2">
          <input v-model="form.newCert" type="text" placeholder="AWS Cloud Practitioner, etc." class="flex-1 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
          <button type="button" @click="addItem('certifications','newCert')" class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900">Añadir</button>
        </div>
        <div class="mt-3 flex flex-wrap gap-2">
          <span v-for="(c,i) in form.certifications" :key="i" class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800">
            {{ c }} <button type="button" @click="removeItem('certifications', i)" class="text-gray-500 hover:text-red-600">×</button>
          </span>
        </div>
        <div v-if="form.errors['certifications']" class="mt-1 text-xs text-red-600">{{ form.errors['certifications'] }}</div>
      </section>

      <!-- Enlaces -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Enlaces</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">LinkedIn</label>
            <input v-model="form.link_linkedin" type="url" placeholder="https://…" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.link_linkedin" class="mt-1 text-xs text-red-600">{{ form.errors.link_linkedin }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Portfolio</label>
            <input v-model="form.link_portfolio" type="url" placeholder="https://…" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.link_portfolio" class="mt-1 text-xs text-red-600">{{ form.errors.link_portfolio }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">GitHub</label>
            <input v-model="form.link_github" type="url" placeholder="https://…" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.link_github" class="mt-1 text-xs text-red-600">{{ form.errors.link_github }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Vídeo presentación</label>
            <input v-model="form.link_video" type="url" placeholder="https://…" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.link_video" class="mt-1 text-xs text-red-600">{{ form.errors.link_video }}</div>
          </div>
        </div>
      </section>

      <!-- Extra -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Extra</h3>
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Aficiones</label>
            <textarea v-model="form.hobbies" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
            <div v-if="form.errors.hobbies" class="mt-1 text-xs text-red-600">{{ form.errors.hobbies }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Clubes / asociaciones</label>
            <textarea v-model="form.clubs" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
            <div v-if="form.errors.clubs" class="mt-1 text-xs text-red-600">{{ form.errors.clubs }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Motivación</label>
            <textarea v-model="form.motivation" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
            <div v-if="form.errors.motivation" class="mt-1 text-xs text-red-600">{{ form.errors.motivation }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Limitaciones</label>
            <textarea v-model="form.limitations" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
            <div v-if="form.errors.limitations" class="mt-1 text-xs text-red-600">{{ form.errors.limitations }}</div>
          </div>
        </div>
      </section>

      <!-- Guardar -->
      <div class="flex items-center justify-end gap-3">
        <span v-if="$page.props.flash?.success" class="text-sm text-green-700 dark:text-green-400">{{ $page.props.flash.success }}</span>
        <button type="submit" @click="submit" :disabled="form.processing"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white disabled:opacity-50 dark:bg-indigo-500">
          Guardar
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

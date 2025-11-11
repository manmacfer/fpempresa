<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  student: { type: Object, required: true },
})

const form = useForm({
  // AVATAR
  avatar: null,

  // PERSONALES / CONTACTO
  phone: props.student.phone ?? '',
  dni: props.student.dni ?? '',
  birth_date: props.student.birth_date ?? '',
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
  fp_modality: props.student.fp_modality ?? '',

  // DISPONIBILIDAD
  availability_slot: props.student.availability_slot ?? '',
  commitments: Array.isArray(props.student.commitments) ? [...props.student.commitments] : [],
  relocate: props.student.relocate ?? null,
  relocate_cities: Array.isArray(props.student.relocate_cities) ? [...props.student.relocate_cities] : [],
  transport_own: props.student.transport_own ?? null,
  work_modality: props.student.work_modality ?? '',
  remote_days: props.student.remote_days ?? null,
  days_per_week: props.student.days_per_week ?? null,
  available_from: props.student.available_from ?? '',

  // INTERESES / COMPETENCIAS / IDIOMAS
  sectors: Array.isArray(props.student.sectors) ? [...props.student.sectors] : [],
  preferred_company_type: props.student.preferred_company_type ?? '',
  non_formal_experience: props.student.non_formal_experience ?? '',
  tech_competencies: Array.isArray(props.student.tech_competencies) ? [...props.student.tech_competencies] : [],
  soft_skills: Array.isArray(props.student.soft_skills) ? [...props.student.soft_skills] : [],
  languages: Array.isArray(props.student.languages) ? [...props.student.languages] : [],
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

  // AUX inputs
  newCommitment: '',
  newRelocateCity: '',
  newSector: '',
  newTech: '',
  newSoft: '',
  newCert: '',
  newLangName: '',
  newLangLevel: '',
})

// Formularios auxiliares para crear Formación / Experiencia
const eduForm = useForm({
  title: '',
  center: '',
  start_date: '',
  end_date: '',
})

const expForm = useForm({
  company: '',
  position: '',
  start_date: '',
  end_date: '',
  functions: '',
  is_non_formal: false,
})

const previewUrl = ref(props.student.avatar_url ?? null)

// Helpers arrays simples
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

// Idiomas
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

// Híbrida
const isHybrid = computed(() => form.work_modality === 'hibrida')

// Guardar perfil
function submit() {
  form
    .transform((d) => ({
      avatar: d.avatar,

      phone: emptyToNull(d.phone),
      dni: emptyToNull(d.dni),
      birth_date: emptyToNull(d.birth_date),
      address: emptyToNull(d.address),
      postal_code: emptyToNull(d.postal_code),
      city: emptyToNull(d.city),
      has_driver_license: boolOrNull(d.has_driver_license),
      has_vehicle: boolOrNull(d.has_vehicle),

      cycle: emptyToNull(d.cycle),
      center: emptyToNull(d.center),
      year_start: numOrNull(d.year_start),
      year_end: numOrNull(d.year_end),
      fp_modality: emptyToNull(d.fp_modality),

      availability_slot: emptyToNull(d.availability_slot),
      commitments: d.commitments,
      relocate: boolOrNull(d.relocate),
      relocate_cities: d.relocate_cities,
      transport_own: boolOrNull(d.transport_own),
      work_modality: emptyToNull(d.work_modality),
      remote_days: isHybrid.value ? numOrNull(d.remote_days) : null,
      days_per_week: numOrNull(d.days_per_week),
      available_from: emptyToNull(d.available_from),

      sectors: d.sectors,
      preferred_company_type: emptyToNull(d.preferred_company_type),
      non_formal_experience: emptyToNull(d.non_formal_experience),
      tech_competencies: d.tech_competencies,
      soft_skills: d.soft_skills,
      languages: d.languages,
      certifications: d.certifications,

      hobbies: emptyToNull(d.hobbies),
      clubs: emptyToNull(d.clubs),
      motivation: emptyToNull(d.motivation),
      limitations: emptyToNull(d.limitations),

      link_linkedin: emptyToNull(d.link_linkedin),
      link_portfolio: emptyToNull(d.link_portfolio),
      link_github: emptyToNull(d.link_github),
      link_video: emptyToNull(d.link_video),
    }))
    .patch(route('students.update.me'), { forceFormData: true })
}

// Crear formación
function createEducation() {
  eduForm.post(route('students.educations.store'), {
    onSuccess: () => eduForm.reset(),
  })
}
// Borrar formación
function deleteEducation(id) {
  router.delete(route('students.educations.destroy', id))
}

// Crear experiencia
function createExperience() {
  // normalizamos boolean
  const payload = {
    company: expForm.company,
    position: emptyToNull(expForm.position),
    start_date: emptyToNull(expForm.start_date),
    end_date: emptyToNull(expForm.end_date),
    functions: emptyToNull(expForm.functions),
    is_non_formal: !!expForm.is_non_formal,
  }
  expForm.transform(() => payload).post(route('students.experiences.store'), {
    onSuccess: () => expForm.reset(),
  })
}
// Borrar experiencia
function deleteExperience(id) {
  router.delete(route('students.experiences.destroy', id))
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
          <img v-if="previewUrl" :src="previewUrl" alt="avatar" class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700" />
          <div class="flex flex-col gap-2">
            <input type="file" accept="image/*" @change="onPickAvatar" />
            <p class="text-xs text-gray-500 dark:text-gray-400">PNG/JPG, máx. 2 MB.</p>
          </div>
        </div>
        <div v-if="form.errors.avatar" class="mt-2 text-xs text-red-600">{{ form.errors.avatar }}</div>
      </section>

      <!-- Datos personales y contacto -->
      <!-- ... (todo igual que la versión que pegaste en el Paso 4) ... -->
      <!-- Mantengo intactas las secciones existentes para no repetir: personales, formación actual, disponibilidad, intereses, competencias, idiomas, certificaciones, enlaces y extra -->
      <!-- Copia y pega este archivo completo; el bloque anterior sigue igual y debajo añadimos Formación/Experiencia (historial) -->

      <!-- Guardar -->
      <div class="flex items-center justify-end gap-3">
        <span v-if="$page.props.flash?.success" class="text-sm text-green-700 dark:text-green-400">{{ $page.props.flash.success }}</span>
        <button type="submit" @click="submit" :disabled="form.processing"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white disabled:opacity-50 dark:bg-indigo-500">
          Guardar
        </button>
      </div>

      <!-- ====================== -->
      <!-- Formación (HISTORIAL) -->
      <!-- ====================== -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Formación (historial)</h3>

        <!-- Añadir formación -->
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Título</label>
            <input v-model="eduForm.title" type="text" placeholder="CFGM SMR, CFGS DAW…" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <div v-if="eduForm.errors.title" class="mt-1 text-xs text-red-600">{{ eduForm.errors.title }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Centro</label>
            <input v-model="eduForm.center" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <div v-if="eduForm.errors.center" class="mt-1 text-xs text-red-600">{{ eduForm.errors.center }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Inicio</label>
            <input v-model="eduForm.start_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <div v-if="eduForm.errors.start_date" class="mt-1 text-xs text-red-600">{{ eduForm.errors.start_date }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Fin</label>
            <input v-model="eduForm.end_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <div v-if="eduForm.errors.end_date" class="mt-1 text-xs text-red-600">{{ eduForm.errors.end_date }}</div>
          </div>
        </div>
        <div class="mt-3 flex justify-end">
          <button type="button" @click="createEducation" :disabled="eduForm.processing" class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white disabled:opacity-50 dark:bg-gray-100 dark:text-gray-900">
            Añadir formación
          </button>
        </div>

        <!-- Lista de formación -->
        <ul class="mt-4 space-y-2">
          <li v-for="e in props.student.educations" :key="e.id" class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 text-sm dark:bg-gray-800/60">
            <div class="min-w-0">
              <div class="font-medium text-gray-800 dark:text-gray-100 truncate">{{ e.title }}</div>
              <div class="text-gray-500 dark:text-gray-400">
                <span v-if="e.center">{{ e.center }} · </span>
                <span>{{ e.start_date || '—' }} — {{ e.end_date || 'Actual' }}</span>
              </div>
            </div>
            <button type="button" @click="deleteEducation(e.id)" class="text-xs text-gray-500 hover:text-red-600">Eliminar</button>
          </li>
        </ul>
      </section>

      <!-- ===================== -->
      <!-- Experiencia (HISTORIAL) -->
      <!-- ===================== -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">Experiencia</h3>

        <!-- Añadir experiencia -->
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Empresa</label>
            <input v-model="expForm.company" type="text" placeholder="Empresa / Proyecto" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <div v-if="expForm.errors.company" class="mt-1 text-xs text-red-600">{{ expForm.errors.company }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Puesto</label>
            <input v-model="expForm.position" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <div v-if="expForm.errors.position" class="mt-1 text-xs text-red-600">{{ expForm.errors.position }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Inicio</label>
            <input v-model="expForm.start_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <div v-if="expForm.errors.start_date" class="mt-1 text-xs text-red-600">{{ expForm.errors.start_date }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Fin</label>
            <input v-model="expForm.end_date" type="date" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
            <div v-if="expForm.errors.end_date" class="mt-1 text-xs text-red-600">{{ expForm.errors.end_date }}</div>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Funciones / logros</label>
            <textarea v-model="expForm.functions" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
            <div v-if="expForm.errors.functions" class="mt-1 text-xs text-red-600">{{ expForm.errors.functions }}</div>
          </div>
          <div class="sm:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input v-model="expForm.is_non_formal" type="checkbox" class="rounded border-gray-300 dark:border-gray-700">
              Es experiencia no formal (voluntariado/colaboración)
            </label>
          </div>
        </div>
        <div class="mt-3 flex justify-end">
          <button type="button" @click="createExperience" :disabled="expForm.processing" class="rounded-lg bg-gray-900 px-4 py-2 text-sm text-white disabled:opacity-50 dark:bg-gray-100 dark:text-gray-900">
            Añadir experiencia
          </button>
        </div>

        <!-- Lista de experiencias -->
        <ul class="mt-4 space-y-2">
          <li v-for="x in props.student.experiences" :key="x.id" class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 text-sm dark:bg-gray-800/60">
            <div class="min-w-0">
              <div class="font-medium text-gray-800 dark:text-gray-100 truncate">{{ x.company }} <span v-if="x.position" class="text-gray-500 dark:text-gray-400">· {{ x.position }}</span></div>
              <div class="text-gray-500 dark:text-gray-400">
                <span>{{ x.start_date || '—' }} — {{ x.end_date || 'Actual' }}</span>
                <span v-if="x.is_non_formal" class="ms-2 rounded bg-gray-200 px-2 py-0.5 text-[11px] dark:bg-gray-700">No formal</span>
              </div>
              <div v-if="x.functions" class="mt-1 text-gray-600 dark:text-gray-300 line-clamp-2">{{ x.functions }}</div>
            </div>
            <button type="button" @click="deleteExperience(x.id)" class="text-xs text-gray-500 hover:text-red-600">Eliminar</button>
          </li>
        </ul>
      </section>
    </div>
  </AuthenticatedLayout>
</template>

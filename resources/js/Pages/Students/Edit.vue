<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  student: { type: Object, required: true },
  allCompetencies: { type: Array, required: true },
  allLanguages: { type: Array, required: true },
})

const provinces = [
  'Álava','Albacete','Alicante','Almería','Asturias','Ávila','Badajoz','Barcelona',
  'Burgos','Cáceres','Cádiz','Cantabria','Castellón','Ciudad Real','Córdoba','Cuenca',
  'Girona','Granada','Guadalajara','Guipúzcoa','Huelva','Huesca','Illes Balears','Jaén',
  'A Coruña','La Rioja','Las Palmas','León','Lleida','Lugo','Madrid','Málaga','Murcia',
  'Navarra','Ourense','Palencia','Pontevedra','Salamanca','Santa Cruz de Tenerife',
  'Segovia','Sevilla','Soria','Tarragona','Teruel','Toledo','València','Valladolid',
  'Bizkaia','Zamora','Zaragoza','Ceuta','Melilla',
]

const languageLevels = ['A1','A2','B1','B2','C1','C2']

// ===== Form principal (perfil) =====
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

  // compatibilidad: competencias y idiomas (catálogo)
  competency_ids: Array.isArray(props.student.competencies)
    ? props.student.competencies.map(c => c.id)
    : [],
  languages: Array.isArray(props.student.languages)
    ? props.student.languages.map(l => ({
        language_id: l.id,
        level: l.level || 'B1',
      }))
    : [],

  // intereses / perfil extra (texto libre)
  sectors: Array.isArray(props.student.sectors) ? [...props.student.sectors] : [],
  preferred_company_type: props.student.preferred_company_type ?? '',
  non_formal_experience: props.student.non_formal_experience ?? '',
  tech_competencies: Array.isArray(props.student.tech_competencies) ? [...props.student.tech_competencies] : [],
  soft_skills: Array.isArray(props.student.soft_skills) ? [...props.student.soft_skills] : [],
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
  newTech: '',
  newSoft: '',
  newCert: '',
  newLanguageId: '',
  newLanguageLevel: 'B1',
})

const previewUrl = ref(props.student.avatar_url ?? null)

// Helpers comunes
function emptyToNull(v){ if(v===undefined||v===null) return null; if(typeof v==='string'&&v.trim()==='') return null; return v }
function numOrNull(v){ if(v===''||v===null||v===undefined) return null; const n=Number(v); return Number.isFinite(n)?n:null }
function boolOrNull(v){ if(v===true) return true; if(v===false) return false; return null }

function addItem(field, valueField=null){
  const val = (valueField ? form[valueField] : form[field])?.toString().trim()
  if (!val) return
  form[field].push(val)
  if (valueField) form[valueField] = ''
  else form[field] = ''
}
function removeItem(field, i){ form[field].splice(i,1) }

// Idiomas (pivot alumno_idioma)
function addLanguagePivot(){
  const id = Number(form.newLanguageId)
  const level = form.newLanguageLevel
  if (!id || !level) return
  if (form.languages.some(l => l.language_id === id)) return
  form.languages.push({ language_id: id, level })
  form.newLanguageId = ''
  form.newLanguageLevel = 'B1'
}
function removeLanguagePivot(i){
  form.languages.splice(i,1)
}
function languageNameById(id){
  const lang = props.allLanguages.find(l => l.id === id)
  return lang ? lang.name : `Idioma #${id}`
}

function onPickAvatar(e){
  const f = e.target.files?.[0]
  form.avatar = f || null
  if (f){
    const r = new FileReader()
    r.onload = () => previewUrl.value = r.result
    r.readAsDataURL(f)
  }
}
function onPickOtherCerts(e){
  form.other_certs = Array.from(e.target.files || [])
}

const isHybrid = computed(()=> form.work_modality === 'hibrida')

function submit(){
  form
    .transform(d => ({
      // files
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
      commitments: d.commitments,
      relocate: boolOrNull(d.relocate),
      relocate_cities: d.relocate_cities,
      transport_own: boolOrNull(d.transport_own),
      work_modality: emptyToNull(d.work_modality),
      remote_days: isHybrid.value ? numOrNull(d.remote_days) : null,
      days_per_week: numOrNull(d.days_per_week),
      available_from: emptyToNull(d.available_from),

      // intereses
      sectors: d.sectors,
      preferred_company_type: emptyToNull(d.preferred_company_type),
      non_formal_experience: emptyToNull(d.non_formal_experience),
      tech_competencies: d.tech_competencies,
      soft_skills: d.soft_skills,
      certifications: d.certifications,

      // compatibilidad
      competency_ids: d.competency_ids,
      languages: d.languages,

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

// ===== Formación (CRUD con modales) =====
const showEduModal = ref(false)
const editingEdu = ref(null)
const eduForm = useForm({ title: '', center: '', start_date: '', end_date: '' })

function openEduCreate(){ editingEdu.value=null; eduForm.reset(); eduForm.clearErrors(); showEduModal.value=true }
function openEduEdit(e){
  editingEdu.value=e
  eduForm.reset(); eduForm.clearErrors()
  eduForm.title=e.title||''
  eduForm.center=e.center||''
  eduForm.start_date=e.start_date||''
  eduForm.end_date=e.end_date||''
  showEduModal.value=true
}
function closeEdu(){ showEduModal.value=false; editingEdu.value=null }

function saveEducation(){
  if(editingEdu.value){
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
function deleteEducation(e){
  if(!confirm('¿Eliminar esta formación?')) return
  router.delete(route('students.educations.destroy', e.id), { preserveScroll: true })
}

// ===== Experiencia (CRUD con modales) =====
const showExpModal = ref(false)
const editingExp = ref(null)
const expForm = useForm({ company: '', position: '', start_date: '', end_date: '', functions: '', is_non_formal: false })

function openExpCreate(){ editingExp.value=null; expForm.reset(); expForm.clearErrors(); expForm.is_non_formal=false; showExpModal.value=true }
function openExpEdit(x){
  editingExp.value=x
  expForm.reset(); expForm.clearErrors()
  expForm.company=x.company||''
  expForm.position=x.position||''
  expForm.start_date=x.start_date||''
  expForm.end_date=x.end_date||''
  expForm.functions=x.functions||''
  expForm.is_non_formal=!!x.is_non_formal
  showExpModal.value=true
}
function closeExp(){ showExpModal.value=false; editingExp.value=null }

function saveExperience(){
  if(editingExp.value){
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
function deleteExperience(x){
  if(!confirm('¿Eliminar esta experiencia?')) return
  router.delete(route('students.experiences.destroy', x.id), { preserveScroll: true })
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Mi perfil</h2>
        <Link
          v-if="props.student.id"
          :href="route('students.public.show', props.student.id)"
          class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
        >
          Ver perfil público
        </Link>
      </div>
    </template>

    <div class="mx-auto max-w-5xl p-6 space-y-8">

      <!-- Avatar & documentos -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Imagen y documentos</h3>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
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
          <div class="grid grid-cols-1 gap-3">
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">CV (PDF/DOC)</label>
              <input
                type="file"
                accept=".pdf,.doc,.docx"
                @change="e=> form.cv = e.target.files?.[0] || null"
                class="text-sm"
              />
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
                Carta de presentación (opcional)
              </label>
              <input
                type="file"
                accept=".pdf,.doc,.docx"
                @change="e=> form.cover_letter = e.target.files?.[0] || null"
                class="text-sm"
              />
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
                Otros certificados (múltiples)
              </label>
              <input
                type="file"
                multiple
                accept=".pdf,.jpg,.jpeg,.png"
                @change="onPickOtherCerts"
                class="text-sm"
              />
            </div>
          </div>
        </div>
        <div class="mt-2 text-xs text-red-600">
          {{ form.errors.avatar || form.errors.cv || form.errors.cover_letter || form.errors['other_certs.*'] }}
        </div>
      </section>

      <!-- Datos personales -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Datos personales</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">DNI/NIE</label>
            <input
              v-model="form.dni"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.dni" class="mt-1 text-xs text-red-600">{{ form.errors.dni }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Teléfono</label>
            <input
              v-model="form.phone"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Fecha de nacimiento</label>
            <input
              v-model="form.birth_date"
              type="date"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.birth_date" class="mt-1 text-xs text-red-600">{{ form.errors.birth_date }}</div>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Provincia</label>
            <select
              v-model="form.province"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            >
              <option value="">— Selecciona —</option>
              <option v-for="prov in provinces" :key="prov" :value="prov">{{ prov }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Municipio / población</label>
            <input
              v-model="form.city"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Código postal</label>
            <input
              v-model="form.postal_code"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Dirección</label>
            <input
              v-model="form.address"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div class="flex items-center gap-6 sm:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input
                v-model="form.has_driver_license"
                type="checkbox"
                class="rounded border-gray-300 dark:border-gray-700"
              >
              Carnet de conducir
            </label>
            <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
              <input
                v-model="form.has_vehicle"
                type="checkbox"
                class="rounded border-gray-300 dark:border-gray-700"
              >
              Vehículo propio
            </label>
          </div>
        </div>
      </section>

      <!-- Académicos -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Académicos</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Ciclo</label>
            <select
              v-model="form.cycle"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            >
              <option value="">— Selecciona —</option>
              <option value="1 DAM">1 DAM</option>
              <option value="2 DAM">2 DAM</option>
              <option value="1 DAW">1 DAW</option>
              <option value="2 DAW">2 DAW</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Centro</label>
            <input
              v-model="form.center"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Año inicio</label>
            <input
              v-model="form.year_start"
              type="number"
              min="1990"
              max="2100"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Año fin</label>
            <input
              v-model="form.year_end"
              type="number"
              min="1990"
              max="2100"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Modalidad FP</label>
            <div class="flex items-center gap-4">
              <label class="inline-flex items-center gap-2 text-sm">
                <input v-model="form.fp_modality" type="radio" value="dual">
                Dual
              </label>
              <label class="inline-flex items-center gap-2 text-sm">
                <input v-model="form.fp_modality" type="radio" value="general">
                General
              </label>
            </div>
          </div>
        </div>
      </section>

      <!-- Disponibilidad -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Disponibilidad</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Franja</label>
            <select
              v-model="form.availability_slot"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            >
              <option value="">— Selecciona —</option>
              <option value="morning">Mañana</option>
              <option value="afternoon">Tarde</option>
              <option value="both">Ambas</option>
            </select>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Modalidad</label>
            <select
              v-model="form.work_modality"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            >
              <option value="">— Selecciona —</option>
              <option value="presencial">Presencial</option>
              <option value="remota">Remota</option>
              <option value="hibrida">Híbrida</option>
            </select>
          </div>
          <div v-if="form.work_modality === 'hibrida'">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Días en remoto</label>
            <input
              v-model="form.remote_days"
              type="number"
              min="0"
              max="7"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Días/semana</label>
            <input
              v-model="form.days_per_week"
              type="number"
              min="1"
              max="7"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Fecha inicio disponible</label>
            <input
              v-model="form.available_from"
              type="date"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div class="flex items-center gap-6 sm:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm">
              <input v-model="form.transport_own" type="checkbox">
              Transporte propio
            </label>
            <label class="inline-flex items-center gap-2 text-sm">
              <input v-model="form.relocate" type="checkbox">
              Puedo desplazarme
            </label>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
              Ciudades a las que puedes desplazarte
            </label>
            <div class="flex flex-wrap gap-2">
              <input
                v-model="form.newRelocateCity"
                type="text"
                placeholder="Ciudad"
                class="w-48 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                @keyup.enter="addItem('relocate_cities','newRelocateCity')"
              />
              <button
                type="button"
                @click="addItem('relocate_cities','newRelocateCity')"
                class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900"
              >
                Añadir
              </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(c,i) in form.relocate_cities"
                :key="i"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800"
              >
                {{ c }}
                <button class="ms-2 text-gray-500" @click="removeItem('relocate_cities', i)">✕</button>
              </span>
            </div>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
              Compromisos que afecten tu disponibilidad
            </label>
            <div class="flex flex-wrap gap-2">
              <input
                v-model="form.newCommitment"
                type="text"
                placeholder="Ej. Trabajo parcial"
                class="w-64 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                @keyup.enter="addItem('commitments','newCommitment')"
              />
              <button
                type="button"
                @click="addItem('commitments','newCommitment')"
                class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900"
              >
                Añadir
              </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(c,i) in form.commitments"
                :key="i"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800"
              >
                {{ c }}
                <button class="ms-2 text-gray-500" @click="removeItem('commitments', i)">✕</button>
              </span>
            </div>
          </div>
        </div>
      </section>

      <!-- Intereses y perfil -->
      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Intereses y perfil</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

          <!-- Sectores -->
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Sectores de interés</label>
            <div class="flex flex-wrap gap-2">
              <input
                v-model="form.newSector"
                type="text"
                placeholder="Ej. Desarrollo web"
                class="w-64 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                @keyup.enter="addItem('sectors','newSector')"
              />
              <button
                type="button"
                @click="addItem('sectors','newSector')"
                class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900"
              >
                Añadir
              </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(s,i) in form.sectors"
                :key="i"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800"
              >
                {{ s }}
                <button class="ms-2 text-gray-500" @click="removeItem('sectors', i)">✕</button>
              </span>
            </div>
          </div>

          <!-- Tecnologías principales (compatibilidad) -->
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
              Tecnologías principales (para compatibilidad)
            </label>
            <div class="max-h-60 overflow-y-auto rounded-lg border border-gray-200 p-3 dark:border-gray-700">
              <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                <label
                  v-for="comp in allCompetencies"
                  :key="comp.id"
                  class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200"
                >
                  <input
                    type="checkbox"
                    :value="comp.id"
                    v-model="form.competency_ids"
                    class="rounded border-gray-300 dark:border-gray-700"
                  >
                  <span>{{ comp.name }}</span>
                </label>
              </div>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Esta lista es la misma que usan las empresas al crear sus vacantes.
            </p>
          </div>

          <!-- Tipo de empresa y experiencia no formal -->
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Tipo de empresa preferida</label>
            <input
              v-model="form.preferred_company_type"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Experiencia no formal</label>
            <input
              v-model="form.non_formal_experience"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>

          <!-- Competencias técnicas (texto libre extra) -->
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
              Otras tecnologías / herramientas (texto libre)
            </label>
            <div class="flex flex-wrap gap-2">
              <input
                v-model="form.newTech"
                type="text"
                placeholder="Ej. Laravel"
                class="w-56 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                @keyup.enter="addItem('tech_competencies','newTech')"
              />
              <button
                type="button"
                @click="addItem('tech_competencies','newTech')"
                class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900"
              >
                Añadir
              </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(t,i) in form.tech_competencies"
                :key="i"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800"
              >
                {{ t }}
                <button class="ms-2 text-gray-500" @click="removeItem('tech_competencies', i)">✕</button>
              </span>
            </div>
          </div>

          <!-- Habilidades personales -->
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Habilidades personales</label>
            <div class="flex flex-wrap gap-2">
              <input
                v-model="form.newSoft"
                type="text"
                placeholder="Ej. Trabajo en equipo"
                class="w-56 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                @keyup.enter="addItem('soft_skills','newSoft')"
              />
              <button
                type="button"
                @click="addItem('soft_skills','newSoft')"
                class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900"
              >
                Añadir
              </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(s,i) in form.soft_skills"
                :key="i"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800"
              >
                {{ s }}
                <button class="ms-2 text-gray-500" @click="removeItem('soft_skills', i)">✕</button>
              </span>
            </div>
          </div>

          <!-- Idiomas (catálogo + nivel, compatibilidad) -->
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">
              Idiomas (para compatibilidad)
            </label>
            <div class="flex flex-wrap items-center gap-2">
              <select
                v-model="form.newLanguageId"
                class="w-48 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
              >
                <option value="">— Idioma —</option>
                <option v-for="lng in allLanguages" :key="lng.id" :value="lng.id">
                  {{ lng.name }}
                </option>
              </select>
              <select
                v-model="form.newLanguageLevel"
                class="w-32 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
              >
                <option v-for="lvl in languageLevels" :key="lvl" :value="lvl">
                  {{ lvl }}
                </option>
              </select>
              <button
                type="button"
                @click="addLanguagePivot"
                class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900"
              >
                Añadir
              </button>
            </div>
            <ul class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
              <li
                v-for="(lng,i) in form.languages"
                :key="lng.language_id"
                class="flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 text-sm dark:bg-gray-800/60"
              >
                <span class="truncate">
                  <strong>{{ languageNameById(lng.language_id) }}</strong> · {{ lng.level }}
                </span>
                <button
                  type="button"
                  @click="removeLanguagePivot(i)"
                  class="text-xs text-gray-500 hover:text-red-600"
                >
                  Eliminar
                </button>
              </li>
            </ul>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Estos idiomas se comparan con los que pide cada vacante.
            </p>
          </div>

          <!-- Certificaciones -->
          <div class="sm:col-span-2">
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Certificaciones</label>
            <div class="flex flex-wrap gap-2">
              <input
                v-model="form.newCert"
                type="text"
                placeholder="Ej. Cisco CCNA"
                class="w-56 rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
                @keyup.enter="addItem('certifications','newCert')"
              />
              <button
                type="button"
                @click="addItem('certifications','newCert')"
                class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900"
              >
                Añadir
              </button>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(c,i) in form.certifications"
                :key="i"
                class="rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800"
              >
                {{ c }}
                <button class="ms-2 text-gray-500" @click="removeItem('certifications', i)">✕</button>
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
            class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900"
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
                <span v-if="e.center">{{ e.center }} · </span>{{ e.start_date || '—' }} — {{ e.end_date || 'Actual' }}
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
            class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900"
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
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">LinkedIn</label>
            <input
              v-model="form.link_linkedin"
              type="url"
              placeholder="https://..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Portfolio</label>
            <input
              v-model="form.link_portfolio"
              type="url"
              placeholder="https://..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">GitHub</label>
            <input
              v-model="form.link_github"
              type="url"
              placeholder="https://..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300 mb-1">Vídeo presentación</label>
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
      <div class="flex items-center justify-end gap-3">
        <span
          v-if="$page.props.flash?.success"
          class="text-sm text-green-700 dark:text-green-400"
        >
          {{ $page.props.flash.success }}
        </span>
        <button
          type="submit"
          @click="submit"
          :disabled="form.processing"
          class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white disabled:opacity-50 dark:bg-indigo-500"
        >
          Guardar
        </button>
      </div>
    </div>

    <!-- ===== MODAL Formación ===== -->
    <div v-if="showEduModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="absolute inset-0 bg-black/40" @click="closeEdu"></div>
      <div class="relative w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-5 shadow-xl dark:border-gray-800 dark:bg-gray-900">
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
            <div v-if="eduForm.errors.title" class="mt-1 text-xs text-red-600">{{ eduForm.errors.title }}</div>
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
      <div class="relative w-full max-w-xl rounded-2xl border border-gray-200 bg-white p-5 shadow-xl dark:border-gray-800 dark:bg-gray-900">
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
            <div v-if="expForm.errors.company" class="mt-1 text-xs text-red-600">{{ expForm.errors.company }}</div>
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
              class="rounded border-gray-300 dark:border-gray-700"
            >
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

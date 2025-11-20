<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MunicipalitySearch from '@/Components/MunicipalitySearch.vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  defaults: { type: Object, default: () => ({}) },
  languages: { type: Array, default: () => [] },
})

const page = usePage()
const flash = computed(() => page.props.flash || {})

const TECH_OPTIONS = [
  // Lenguajes Backend
  { category: 'Backend', items: ['.NET', 'C#', 'Django', 'Express', 'Flask', 'Go', 'Java', 'Laravel', 'Node.js', 'PHP', 'Python', 'Ruby on Rails', 'Rust', 'Spring Boot', 'Symfony'] },
  // Frontend
  { category: 'Frontend', items: ['Angular', 'JavaScript', 'jQuery', 'Next.js', 'Nuxt', 'React', 'Svelte', 'TypeScript', 'Vue'] },
  // Bases de Datos
  { category: 'Bases de Datos', items: ['Elasticsearch', 'MariaDB', 'MongoDB', 'MySQL', 'PostgreSQL', 'Redis'] },
  // DevOps & Cloud
  { category: 'DevOps & Cloud', items: ['Ansible', 'Apache', 'AWS', 'Azure', 'CI/CD', 'Docker', 'GCP', 'GitHub Actions', 'GitLab CI', 'Kubernetes', 'Nginx', 'Terraform'] },
  // APIs & Mensajería
  { category: 'APIs & Mensajería', items: ['GraphQL', 'gRPC', 'Kafka', 'RabbitMQ', 'REST'] },
  // CSS & Build Tools
  { category: 'CSS & Build Tools', items: ['CSS', 'HTML', 'Less', 'Sass', 'Tailwind', 'Vite', 'Webpack'] },
  // Testing
  { category: 'Testing', items: ['Cypress', 'Jest', 'Pest', 'PHPUnit', 'Playwright', 'Vitest'] },
]

const form = useForm({
  title: '',
  description: '',
  cycle_required: '',
  mode: props.defaults.mode ?? 'presencial',
  city: '',
  province: '',
  hours_per_week: null,
  duration_weeks: null,
  paid: props.defaults.paid ?? false,
  salary_month: null,
  tech_stack: [],
  soft_skills: '',
  status: 'published',
  languages_required: [],
  accepts_fp_general: true, // Checkbox para FP General
  accepts_fp_dual: true,    // Checkbox para FP Dual
  availability_slot: props.defaults.availability_slot ?? '',
})

function handleMunicipalitySelect(municipality) {
  form.city = municipality.name
  form.province = municipality.province_name
}

function submit() {
  form.status = 'published'
  form.transform((data) => ({
    ...data,
    soft_skills: data.soft_skills ? data.soft_skills.split(',').map(s => s.trim()).filter(Boolean) : []
  })).post(route('vacancies.store'), { preserveScroll: true })
}

const toggle = (arr, val) => {
  const i = arr.indexOf(val)
  if (i === -1) arr.push(val)
  else arr.splice(i, 1)
}

const isChecked = (arr, val) => arr.includes(val)

const levelOptions = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2']
const addLang = () => form.languages_required.push({ language_id: null, min_level: 'B1' })
const removeLang = (idx) => form.languages_required.splice(idx, 1)

const langLabel = (l) => l?.name ?? l?.nombre ?? '—'
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Crear vacante" />

    <!-- Header con gradiente sutil -->
    <div class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Nueva vacante</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Completa los detalles de la oferta que deseas publicar</p>
          </div>
          <Link 
            :href="route('vacancies.my')" 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium
                   bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300
                   border border-gray-200 dark:border-gray-700
                   hover:bg-gray-50 dark:hover:bg-gray-700/50
                   transition-all duration-200 shadow-sm hover:shadow"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Mis vacantes
          </Link>
        </div>

        <div v-if="flash.success" class="mb-6 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-5 py-4 dark:from-green-900/20 dark:to-emerald-900/20 dark:border-green-800 dark:text-green-200 shadow-sm">
          <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ flash.success }}</span>
          </div>
        </div>

        <div v-if="Object.keys(form.errors).length" class="mb-6 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 text-red-800 px-5 py-4 dark:from-red-900/20 dark:to-pink-900/20 dark:border-red-800 dark:text-red-200 shadow-sm">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <ul class="space-y-1 text-sm">
              <li v-for="(msg, key) in form.errors" :key="key">{{ msg }}</li>
            </ul>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <section class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-8 space-y-6 backdrop-blur-sm">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">· Título de la vacante</label>
            <input 
              v-model="form.title" 
              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                     focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                     transition-all duration-200 shadow-sm"
              placeholder="Ej: Desarrollador Full Stack Junior"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">· Descripción</label>
            <textarea 
              v-model="form.description" 
              rows="6" 
              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                     focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                     transition-all duration-200 shadow-sm resize-none"
              placeholder="Describe las responsabilidades, requisitos y qué ofreces..."
            ></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">· Ciclo requerido</label>
              <select 
                v-model="form.cycle_required" 
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                       focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                       transition-all duration-200 shadow-sm"
              >
                <option value="">— Selecciona —</option>
                <option value="1DAM">1 DAM</option>
                <option value="2DAM">2 DAM</option>
                <option value="1DAW">1 DAW</option>
                <option value="2DAW">2 DAW</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">· Franja horaria</label>
              <select 
                v-model="form.availability_slot"
                required
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                       focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                       transition-all duration-200 shadow-sm"
              >
                <option value="">— Selecciona —</option>
                <option value="manana">Mañana</option>
                <option value="tarde">Tarde</option>
                <option value="completa">Completa</option>
              </select>
              <div v-if="form.errors.availability_slot" class="text-xs text-red-600 mt-1">{{ form.errors.availability_slot }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">· Modalidad</label>
              <select 
                v-model="form.mode" 
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                       focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                       transition-all duration-200 shadow-sm"
              >
                <option value="presencial">Presencial</option>
                <option value="remoto">Teletrabajo</option>
                <option value="hibrido">Híbrido</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2">
              <MunicipalitySearch
                label="· Municipio y Provincia"
                placeholder="Escribe para buscar el municipio..."
                @select="handleMunicipalitySelect"
                :error="form.errors.city || form.errors.province"
              />
              <div v-if="form.city && form.province" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Seleccionado: <strong>{{ form.city }}</strong> ({{ form.province }})
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">· Horas/semana</label>
              <input 
                v-model.number="form.hours_per_week" 
                type="number" 
                min="1" 
                max="40" 
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                       focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                       transition-all duration-200 shadow-sm"
                placeholder="Ej: 40"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">· Duración (semanas)</label>
              <input 
                v-model.number="form.duration_weeks" 
                type="number" 
                min="1" 
                max="52" 
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                       focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                       transition-all duration-200 shadow-sm"
                placeholder="Ej: 12"
              />
            </div>
            <div class="flex items-end">
              <label class="inline-flex items-center gap-3 cursor-pointer px-4 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                <input 
                  v-model="form.paid" 
                  type="checkbox" 
                  class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800 w-5 h-5"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Remunerada</span>
              </label>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">· Salario (€/mes)</label>
              <input 
                v-model.number="form.salary_month" 
                type="number" 
                min="0" 
                :disabled="!form.paid"
                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                       focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                       transition-all duration-200 shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                placeholder="Ej: 800"
              />
            </div>
          </div>

          <!-- Tecnologías (checkboxes por categorías) -->
          <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-4">· Tecnologías requeridas</label>
            <div v-for="group in TECH_OPTIONS" :key="group.category" class="mb-5">
              <h4 class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-3">
                {{ group.category }}
              </h4>
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <label 
                  v-for="t in group.items" 
                  :key="t" 
                  class="inline-flex items-center gap-2.5 text-sm text-gray-700 dark:text-gray-300
                         px-3 py-2 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/20
                         transition-colors duration-150 cursor-pointer"
                >
                  <input
                    type="checkbox"
                    :value="t"
                    :checked="isChecked(form.tech_stack, t)"
                    @change="toggle(form.tech_stack, t)"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 w-4 h-4"
                  />
                  <span class="select-none">{{ t }}</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Soft skills (texto libre) -->
          <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">· Soft skills</label>
            <input
              v-model="form.soft_skills"
              type="text"
              placeholder="Ej: Trabajo en equipo, Comunicación, Proactividad"
              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                     focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                     transition-all duration-200 shadow-sm"
            />
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Separa las habilidades con comas (,)
            </p>
          </div>

          <!-- Idiomas requeridos -->
          <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="mb-3 flex items-center justify-between">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">· Idiomas requeridos</label>
              <button 
                type="button"
                @click="addLang"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white
                       hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600
                       transition-all duration-200 shadow-sm hover:shadow"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Añadir idioma
              </button>
            </div>

            <p v-if="!form.languages_required.length" class="text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/30 rounded-xl px-4 py-3">
              Si lo dejas vacío, no se requerirá ningún idioma específico.
            </p>

            <div v-for="(row, idx) in form.languages_required" :key="idx" class="mb-3 grid grid-cols-1 gap-3 md:grid-cols-3 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
              <select 
                v-model.number="row.language_id"
                class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                       focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                       transition-all duration-200 shadow-sm"
              >
                <option :value="null" disabled>Idioma…</option>
                <option v-for="l in props.languages" :key="l.id" :value="l.id">{{ langLabel(l) }}</option>
              </select>

              <select 
                v-model="row.min_level"
                class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700/50 dark:text-gray-100
                       focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:focus:ring-indigo-400
                       transition-all duration-200 shadow-sm"
              >
                <option v-for="lv in levelOptions" :key="lv" :value="lv">Nivel {{ lv }}</option>
              </select>

              <button 
                type="button"
                @click="removeLang(idx)"
                class="rounded-xl border border-red-200 px-3 py-2 text-sm font-medium text-red-600
                       hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-900/20
                       transition-colors duration-150"
              >
                Eliminar
              </button>
            </div>
          </div>

          <!-- Checkboxes para FP General y FP Dual -->
          <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-3">· Modalidad de FP aceptada</label>
            <div class="flex items-center gap-6">
              <label class="inline-flex items-center gap-3 cursor-pointer px-4 py-2.5 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                <input 
                  type="checkbox" 
                  v-model="form.accepts_fp_general" 
                  class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 w-5 h-5"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">FP General</span>
              </label>
              <label class="inline-flex items-center gap-3 cursor-pointer px-4 py-2.5 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors">
                <input 
                  type="checkbox" 
                  v-model="form.accepts_fp_dual" 
                  class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 w-5 h-5"
                />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">FP Dual</span>
              </label>
            </div>
          </div>
        </section>

        <!-- Sidebar publicar -->
        <aside class="bg-gradient-to-br from-white to-indigo-50/30 dark:from-gray-800 dark:to-indigo-950/20
                      rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-6 space-y-4 h-fit sticky top-6">
          <div class="text-center pb-4 border-b border-gray-200 dark:border-gray-700">
            <div class="mx-auto w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-3 shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Publicar vacante</h2>
          </div>
          <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
            La vacante se publicará inmediatamente y será visible para todos los estudiantes que cumplan los requisitos.
          </p>
          <button 
            @click="submit" 
            :disabled="form.processing"
            class="w-full px-5 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold
                   hover:from-indigo-700 hover:to-purple-700
                   disabled:opacity-50 disabled:cursor-not-allowed
                   transition-all duration-200 shadow-lg hover:shadow-xl
                   transform hover:-translate-y-0.5 active:translate-y-0"
          >
            <span v-if="form.processing">Publicando...</span>
            <span v-else class="flex items-center justify-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              Publicar ahora
            </span>
          </button>
        </aside>
      </div>
    </div>
  </div>
  </AuthenticatedLayout>
</template>
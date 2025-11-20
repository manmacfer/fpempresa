<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MunicipalitySearch from '@/Components/MunicipalitySearch.vue'
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  vacancy: { type: Object, required: true },
  languages: { type: Array, default: () => [] },
})

// Opciones de tecnología por categorías
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

const page = usePage()
const flash = ref(page.props.flash || {})

// Inicializa tech_stack como array y marca las que ya existan
const form = useForm({
  title: props.vacancy.title ?? '',
  description: props.vacancy.description ?? '',
  cycle_required: props.vacancy.cycle_required ?? '',
  mode: props.vacancy.mode ?? '',
  city: props.vacancy.city ?? '',
  province: props.vacancy.province ?? '',
  hours_per_week: props.vacancy.hours_per_week ?? '',
  duration_weeks: props.vacancy.duration_weeks ?? '',
  paid: !!props.vacancy.paid,
  salary_month: props.vacancy.salary_month ?? '',
  status: props.vacancy.status ?? 'published',
  tech_stack: Array.isArray(props.vacancy.tech_stack)
    ? props.vacancy.tech_stack.slice()
    : (props.vacancy.tech_stack ? String(props.vacancy.tech_stack).split(',').map(s => s.trim()).filter(Boolean) : []),
  soft_skills: Array.isArray(props.vacancy.soft_skills) 
    ? props.vacancy.soft_skills.join(', ')
    : (props.vacancy.soft_skills ? String(props.vacancy.soft_skills) : ''),
  languages_required: props.vacancy.required_languages ?? [],
  accepts_fp_general: !!props.vacancy.accepts_fp_general,
  accepts_fp_dual: !!props.vacancy.accepts_fp_dual,
  availability_slot: props.vacancy.availability_slot ?? '',
})

function handleMunicipalitySelect(municipality) {
  form.city = municipality.name
  form.province = municipality.province_name
}

function submit() {
  // no tocamos tech_stack: se envía directamente desde checkboxes
  form.transform((data) => ({
    ...data,
    soft_skills: data.soft_skills ? data.soft_skills.split(',').map(s => s.trim()).filter(Boolean) : []
  })).patch(route('vacancies.update', props.vacancy.id), {
    preserveScroll: true,
  })
}

const deleteForm = useForm({})
function deleteVacancy() {
  if (!confirm('¿Eliminar esta vacante? Esta acción no se puede deshacer.')) return
  deleteForm.delete(route('vacancies.destroy', props.vacancy.id), {
    preserveScroll: true,
    onSuccess: () => {
      try { router.visit(route('vacancies.my')) } catch (_) {}
    },
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Editar: ${props.vacancy.title || 'Vacante'}`" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Header con gradiente -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-100 bg-gradient-to-br from-indigo-600 to-purple-600 p-8 shadow-lg dark:border-gray-800 dark:from-indigo-700 dark:to-purple-700">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
              <div class="mb-2 flex items-center gap-2 text-sm text-indigo-100">
                <Link :href="route('vacancies.show', props.vacancy.id)" class="hover:text-white transition-colors">
                  ← Volver a vacante
                </Link>
              </div>
              <h1 class="text-3xl font-bold text-white">Editar vacante</h1>
              <p class="mt-2 text-indigo-100">{{ props.vacancy.title }}</p>
            </div>
            <button
              @click="deleteVacancy"
              class="inline-flex items-center gap-2 rounded-xl border-2 border-white/30 bg-white/10 px-5 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:bg-white/20"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              Eliminar
            </button>
          </div>
        </div>

        <section class="mb-6 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
          <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">Información básica</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 gap-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
            <input v-model="form.title" type="text"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            <div v-if="form.errors.title" class="text-xs text-red-600">{{ form.errors.title }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
            <textarea v-model="form.description" rows="6"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"></textarea>
            <div v-if="form.errors.description" class="text-xs text-red-600">{{ form.errors.description }}</div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciclo requerido</label>
            <select v-model="form.cycle_required" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <option value="">—</option>
                <option value="1DAM">1 DAM</option>
                <option value="2DAM">2 DAM</option>
                <option value="1DAW">1 DAW</option>
                <option value="2DAW">2 DAW</option>
            </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modalidad</label>
              <select v-model="form.mode"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
                <option value="">— Selecciona —</option>
                <option value="presencial">Presencial</option>
                <option value="remoto">Teletrabajo</option>
                <option value="hibrido">Híbrido</option>
              </select>
            </div>
            <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Franja horaria</label>
            <select v-model="form.availability_slot"
                    required
                    class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <option value="">— Selecciona —</option>
                <option value="manana">Mañana</option>
                <option value="tarde">Tarde</option>
                <option value="completa">Completa</option>
            </select>
            <div v-if="form.errors.availability_slot" class="text-xs text-red-600">{{ form.errors.availability_slot }}</div>
            </div>
            <div class="sm:col-span-2">
              <MunicipalitySearch
                label="Municipio y Provincia"
                placeholder="Escribe para buscar el municipio..."
                @select="handleMunicipalitySelect"
                :error="form.errors.city || form.errors.province"
              />
              <div v-if="form.city && form.province" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Seleccionado: <strong>{{ form.city }}</strong> ({{ form.province }})
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Horas / semana</label>
              <input v-model="form.hours_per_week" type="number" min="1"
                     class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duración (semanas)</label>
              <input v-model="form.duration_weeks" type="number" min="1"
                     class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
            </div>
            <div class="flex items-center gap-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remunerada</label>
              <input v-model="form.paid" type="checkbox" class="ml-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Salario (€/mes)</label>
            <input v-model="form.salary_month" type="number" min="0"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
          </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Modalidad de FP aceptada</label>
            <div class="flex items-center gap-4 mt-2">
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" v-model="form.accepts_fp_general" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800" />
                FP General
              </label>
              <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" v-model="form.accepts_fp_dual" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800" />
                FP Dual
              </label>
            </div>
          </div>

          <!-- Tecnologías por categorías -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Tecnologías requeridas</label>
            <div v-for="group in TECH_OPTIONS" :key="group.category" class="mb-4">
              <h4 class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">{{ group.category }}</h4>
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                <label v-for="t in group.items" :key="t" class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                  <input
                    type="checkbox"
                    :value="t"
                    v-model="form.tech_stack"
                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-800"
                  />
                  <span>{{ t }}</span>
                </label>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Soft skills</label>
            <input
              v-model="form.soft_skills"
              type="text"
              placeholder="Ej: Trabajo en equipo, Comunicación, Proactividad"
              class="w-full rounded-xl border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Separa las habilidades con comas (,)</p>
          </div>
        </div>
        </div>

        <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
          <Link href="/vacantes" class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
            Cancelar
          </Link>
          <button @click="submit" :disabled="form.processing"
                  class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl disabled:opacity-50 disabled:hover:scale-100 dark:from-indigo-500 dark:to-purple-500">
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
      </section>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
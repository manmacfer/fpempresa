<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  defaults: { type: Object, default: () => ({}) },
})

const page = usePage()
const flash = computed(() => page.props.flash || {})

const TECH_OPTIONS = [
  // Backend / Lenguajes
  'PHP','Laravel','Symfony','Java','Spring Boot','.NET','C#','Node.js','Express','Python','Django','Flask','Ruby on Rails','Go','Rust',
  // Frontend
  'JavaScript','TypeScript','Vue','React','Angular','Svelte','jQuery','Next.js','Nuxt',
  // Datos
  'MySQL','PostgreSQL','MariaDB','MongoDB','Redis','Elasticsearch',
  // Cloud / DevOps
  'Docker','Kubernetes','Nginx','Apache','CI/CD','GitHub Actions','GitLab CI','AWS','Azure','GCP','Terraform','Ansible',
  // APIs / Mensajería
  'REST','GraphQL','gRPC','RabbitMQ','Kafka',
  // Estilos / Build
  'HTML','CSS','Sass','Less','Tailwind','Vite','Webpack',
  // Testing
  'PHPUnit','Pest','Jest','Vitest','Cypress','Playwright',
]

const SOFT_OPTIONS = [
  'Trabajo en equipo','Comunicación','Proactividad','Resolución de problemas','Aprendizaje rápido','Adaptabilidad',
  'Atención al detalle','Gestión del tiempo','Pensamiento crítico'
]

const form = useForm({
  title: '',
  description: '',
  cycle_required: '',
  mode: props.defaults.mode ?? 'onsite',
  city: '',
  province: '',
  hours_per_week: null,
  duration_weeks: null,
  paid: props.defaults.paid ?? false,
  salary_month: null,
  tech_stack: [],       // ← checkboxes
  soft_skills: [],      // ← checkboxes (dejamos también como array)
  status: props.defaults.status ?? 'draft',
})

function submit(publish = false) {
  if (publish) form.status = 'published'
  form.post(route('vacancies.store'), { preserveScroll: true })
}

// Helpers para checkboxes
const toggle = (arr, val) => {
  const i = arr.indexOf(val)
  if (i === -1) arr.push(val); else arr.splice(i,1)
}
const isChecked = (arr, val) => arr.includes(val)
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Crear vacante" />

    <div class="max-w-5xl mx-auto p-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Crear vacante</h1>
        <Link :href="route('vacancies.my')" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
          Mis vacantes
        </Link>
      </div>

      <div v-if="flash.success" class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3 dark:bg-green-900/30 dark:text-green-200">
        {{ flash.success }}
      </div>
      <div v-if="Object.keys(form.errors).length" class="mb-4 rounded-xl bg-red-50 text-red-800 px-4 py-3 dark:bg-red-900/30 dark:text-red-200">
        <ul class="list-disc ml-5 space-y-1">
          <li v-for="(msg, key) in form.errors" :key="key">{{ msg }}</li>
        </ul>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna principal -->
        <section class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow p-6 space-y-5">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Título</label>
            <input v-model="form.title" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
          </div>

          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Descripción</label>
            <textarea v-model="form.description" rows="6" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Ciclo requerido</label>
              <select v-model="form.cycle_required" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <option value="">—</option>
                <option value="1DAM">1 DAM</option>
                <option value="2DAM">2 DAM</option>
                <option value="1DAW">1 DAW</option>
                <option value="2DAW">2 DAW</option>
              </select>
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Modalidad</label>
              <select v-model="form.mode" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                <option value="onsite">Presencial</option>
                <option value="remote">Remoto</option>
                <option value="hybrid">Híbrido</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Ciudad</label>
              <input v-model="form.city" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Provincia</label>
              <input v-model="form.province" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Horas/semana</label>
              <input v-model.number="form.hours_per_week" type="number" min="1" max="40" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Semanas</label>
              <input v-model.number="form.duration_weeks" type="number" min="1" max="52" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div class="flex items-center gap-2">
              <label class="block text-sm text-gray-600 dark:text-gray-300">Remunerada</label>
              <input v-model="form.paid" type="checkbox" class="align-middle" />
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Salario (€/mes)</label>
              <input v-model.number="form.salary_month" type="number" min="0" :disabled="!form.paid"
                     class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" />
            </div>
          </div>

          <!-- Tecnologías (checkboxes) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Tecnologías requeridas</label>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
              <label v-for="t in TECH_OPTIONS" :key="t" class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input
                  type="checkbox"
                  :value="t"
                  :checked="isChecked(form.tech_stack, t)"
                  @change="toggle(form.tech_stack, t)"
                  class="rounded border-gray-300 dark:border-gray-600"
                />
                <span>{{ t }}</span>
              </label>
            </div>
          </div>

          <!-- Soft skills (checkboxes) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Soft skills</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
              <label v-for="s in SOFT_OPTIONS" :key="s" class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input
                  type="checkbox"
                  :value="s"
                  :checked="isChecked(form.soft_skills, s)"
                  @change="toggle(form.soft_skills, s)"
                  class="rounded border-gray-300 dark:border-gray-600"
                />
                <span>{{ s }}</span>
              </label>
            </div>
          </div>
        </section>

        <!-- Sidebar estado -->
        <aside class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 space-y-4">
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Estado</h2>
          <label class="inline-flex items-center gap-2">
            <input type="radio" value="draft" v-model="form.status" />
            <span>Borrador</span>
          </label>
          <label class="inline-flex items-center gap-2">
            <input type="radio" value="published" v-model="form.status" />
            <span>Publicada</span>
          </label>

          <div class="pt-4 flex flex-col gap-2">
            <button @click="submit(false)" :disabled="form.processing"
                    class="px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-black disabled:opacity-60 dark:bg-gray-700 dark:hover:bg-gray-600">
              Guardar borrador
            </button>
            <button @click="submit(true)" :disabled="form.processing"
                    class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60">
              Publicar
            </button>
          </div>
        </aside>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

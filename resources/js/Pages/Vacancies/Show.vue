<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  vacancy: { type: Object, required: true },
})

const page = usePage()
const auth = computed(() => page.props.auth || {})

// Normalización mínima de rol (company/student)
const rawRole = computed(() => auth.value.roleSlug ?? auth.value.role ?? null)
const normalize = (v) => {
  if (!v) return null
  const s = String(v).toLowerCase().trim()
  if (['student','students','alumno','alumna','alumnos','alumnas','estudiante','estudiantes'].includes(s)) return 'student'
  if (['company','companies','empresa','empresas','compania','compañia','compañía'].includes(s)) return 'company'
  return s
}
const role = computed(() => normalize(rawRole.value) ?? 'student')

const isCompany = computed(() => role.value === 'company')
const isOwner   = computed(() => isCompany.value && auth.value.companyId && auth.value.companyId === props.vacancy.company_id)

const companyName = computed(() =>
  props.vacancy.company?.trade_name ||
  props.vacancy.company?.legal_name ||
  'Empresa'
)

// Si existe la ruta matchings.index, generamos URL con ?vacancy_id=...
const matchUrl = computed(() => {
  try {
    if (route().has && route().has('matchings.index')) {
      return route('matchings.index', { vacancy_id: props.vacancy.id })
    }
  } catch (_) {}
  return null
})

const badge = (text, tone = 'gray') => {
  const tones = {
    gray:   'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    indigo: 'bg-indigo-50 text-indigo-700 dark:bg-indigo-400/20 dark:text-indigo-200',
    green:  'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
  }
  return `inline-flex items-center rounded-full px-3 py-1 text-xs font-medium ${tones[tone] || tones.gray}`
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="props.vacancy?.title || 'Vacante'" />

    <div class="mx-auto max-w-5xl p-6">
      <!-- Header -->
      <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100">
            {{ props.vacancy.title }}
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Empresa: <b>{{ companyName }}</b>
          </p>

          <div class="mt-3 flex flex-wrap gap-2">
            <span v-if="props.vacancy.cycle_required" :class="badge(props.vacancy.cycle_required,'indigo')">
              {{ props.vacancy.cycle_required }}
            </span>
            <span v-if="props.vacancy.mode" :class="badge(props.vacancy.mode)">
              {{ props.vacancy.mode }}
            </span>
            <span v-if="props.vacancy.city || props.vacancy.province" :class="badge('','gray')">
              <span class="truncate">
                {{ props.vacancy.city }}<span v-if="props.vacancy.city && props.vacancy.province"> — </span>{{ props.vacancy.province }}
              </span>
            </span>
            <span v-if="props.vacancy.hours_per_week" :class="badge(props.vacancy.hours_per_week + ' h/sem')">
              {{ props.vacancy.hours_per_week }} h/sem
            </span>
            <span v-if="props.vacancy.duration_weeks" :class="badge(props.vacancy.duration_weeks + ' semanas')">
              {{ props.vacancy.duration_weeks }} semanas
            </span>
            <span v-if="props.vacancy.paid" :class="badge('Remunerada','green')">
              Remunerada
            </span>
            <span v-if="props.vacancy.status" :class="badge(props.vacancy.status, props.vacancy.status==='published'?'green':'gray')">
              {{ props.vacancy.status }}
            </span>
          </div>
        </div>

        <!-- Acciones -->
        <div class="flex items-center gap-2">
          <!-- Botón Match solo para empresas -->
          <Link
            v-if="isCompany && matchUrl"
            :href="matchUrl"
            class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-60"
          >
            Ver Match
          </Link>

          <!-- Volver -->
          <Link
            :href="route('vacancies.my')"
            class="inline-flex items-center rounded-xl bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-black dark:bg-gray-700 dark:hover:bg-gray-600"
            v-if="isCompany"
          >
            Mis vacantes
          </Link>
          <Link
            :href="route('vacancies.index')"
            class="inline-flex items-center rounded-xl border px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800/70"
            v-else
          >
            Volver al listado
          </Link>
        </div>
      </div>

      <!-- Contenido -->
      <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
        <!-- Descripción -->
        <section class="md:col-span-2 rounded-2xl bg-white p-6 shadow dark:bg-gray-800">
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Descripción</h2>
          <div class="mt-3 text-gray-700 dark:text-gray-300 whitespace-pre-line">
            <template v-if="props.vacancy.description && props.vacancy.description.trim()">
              {{ props.vacancy.description }}
            </template>
            <em v-else class="text-gray-500 dark:text-gray-400">Sin descripción.</em>
          </div>
        </section>

        <!-- Lateral -->
        <aside class="rounded-2xl bg-white p-6 shadow dark:bg-gray-800">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detalles</h3>
          <dl class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-300">
            <div v-if="props.vacancy.salary_month && props.vacancy.paid" class="flex justify-between">
              <dt>Salario aprox.</dt>
              <dd><b>{{ new Intl.NumberFormat('es-ES').format(props.vacancy.salary_month) }} € / mes</b></dd>
            </div>
            <div v-if="props.vacancy.published_at" class="flex justify-between">
              <dt>Publicada</dt>
              <dd>{{ new Date(props.vacancy.published_at).toLocaleString('es-ES') }}</dd>
            </div>
          </dl>

          <div v-if="props.vacancy.tech_stack?.length" class="mt-5">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Tecnologías</h4>
            <div class="mt-2 flex flex-wrap gap-2">
              <span v-for="t in props.vacancy.tech_stack" :key="t" class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700 dark:bg-gray-900 dark:text-gray-300">
                {{ t }}
              </span>
            </div>
          </div>

          <div v-if="props.vacancy.soft_skills?.length" class="mt-5">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Soft skills</h4>
            <div class="mt-2 flex flex-wrap gap-2">
              <span v-for="s in props.vacancy.soft_skills" :key="s" class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700 dark:bg-gray-900 dark:text-gray-300">
                {{ s }}
              </span>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

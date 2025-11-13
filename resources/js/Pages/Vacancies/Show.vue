<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

const props = defineProps({
  vacancy: { type: Object, required: true },
})

const companyName = computed(() => props.vacancy.company?.trade_name || props.vacancy.company?.legal_name || 'Empresa')
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="props.vacancy.title" />

    <div class="max-w-5xl mx-auto p-6">
      <div class="rounded-2xl bg-white dark:bg-gray-800 shadow p-6 md:p-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100">{{ props.vacancy.title }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Empresa: <b>{{ companyName }}</b></p>

        <div class="mt-4 flex flex-wrap gap-2 text-xs">
          <span v-if="props.vacancy.cycle_required" class="rounded-full bg-indigo-50 px-3 py-1 text-indigo-700 dark:bg-indigo-400/20 dark:text-indigo-200">
            {{ props.vacancy.cycle_required }}
          </span>
          <span v-if="props.vacancy.mode" class="rounded-full bg-gray-100 px-3 py-1 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
            {{ props.vacancy.mode }}
          </span>
          <span v-if="props.vacancy.city || props.vacancy.province" class="rounded-full bg-gray-100 px-3 py-1 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
            {{ props.vacancy.city }}<span v-if="props.vacancy.city && props.vacancy.province"> — </span>{{ props.vacancy.province }}
          </span>
          <span v-if="props.vacancy.hours_per_week" class="rounded-full bg-gray-100 px-3 py-1 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
            {{ props.vacancy.hours_per_week }} h/sem
          </span>
          <span v-if="props.vacancy.duration_weeks" class="rounded-full bg-gray-100 px-3 py-1 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
            {{ props.vacancy.duration_weeks }} semanas
          </span>
          <span v-if="props.vacancy.paid" class="rounded-full bg-green-100 px-3 py-1 text-green-800 dark:bg-green-900/30 dark:text-green-200">
            Remunerada
          </span>
        </div>

        <div class="mt-6 prose dark:prose-invert max-w-none">
          <p v-if="props.vacancy.description" class="whitespace-pre-line">{{ props.vacancy.description }}</p>
          <p v-else class="italic text-gray-500 dark:text-gray-400">Sin descripción.</p>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-if="props.vacancy.tech_stack?.length" class="rounded-2xl bg-gray-50 dark:bg-gray-900 p-4">
            <h3 class="font-semibold">Tecnologías</h3>
            <ul class="mt-2 list-disc ms-5">
              <li v-for="t in props.vacancy.tech_stack" :key="t">{{ t }}</li>
            </ul>
          </div>

          <div v-if="props.vacancy.soft_skills?.length" class="rounded-2xl bg-gray-50 dark:bg-gray-900 p-4">
            <h3 class="font-semibold">Soft skills</h3>
            <ul class="mt-2 list-disc ms-5">
              <li v-for="s in props.vacancy.soft_skills" :key="s">{{ s }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  role: { type: String, default: null }
})

const page = usePage()
const effectiveRole = computed(() => props.role ?? page.props?.auth?.user?.role ?? '—')
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Dashboard" />

    <template #header>
      <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Dashboard</h2>
    </template>

    <div class="py-6">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow
                    dark:border-gray-800 dark:bg-gray-900">
          <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Bienvenido</h1>

          <p class="mt-2 text-gray-700 dark:text-gray-300">
            Rol actual: <span class="font-semibold">{{ effectiveRole }}</span>
          </p>

          <div class="mt-6">
            <Link
              :href="route('vacancies.index')"
              class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium
                     text-white bg-indigo-600 hover:bg-indigo-700
                     focus:outline-none focus:ring-2 focus:ring-indigo-500/50
                     dark:bg-indigo-500 dark:hover:bg-indigo-400 dark:focus:ring-indigo-400/40"
            >
              Ir a Vacantes
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

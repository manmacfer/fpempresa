<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const auth = computed(() => page.props.auth || {})
const role = computed(() => auth.value.role)
const studentId = computed(() => auth.value.studentId)
const companyId = computed(() => auth.value.companyId)

const myEditorRoute = computed(() => {
  return role.value === 'company'
    ? route('companies.edit.me')
    : route('students.edit.me')
})

const myPublicRoute = computed(() => {
  if (role.value === 'company' && companyId.value) {
    return route('companies.public.show', companyId.value)
  }
  if (studentId.value) {
    return route('students.public.show', studentId.value)
  }
  return '#'
})
</script>

<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-6">
          <Link :href="route('dashboard')" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            FP Empresa
          </Link>

          <!-- Menú por rol -->
          <div class="hidden md:flex items-center gap-4">
            <!-- Empresa -->
            <template v-if="role==='company'">
              <Link :href="route('companies.edit.me')" class="text-sm text-gray-700 dark:text-gray-200 hover:text-indigo-600">
                Vacantes
              </Link>
              <Link :href="route('companies.edit.me') + '#matchings'" class="text-sm text-gray-700 dark:text-gray-200 hover:text-indigo-600">
                Matchings
              </Link>
            </template>

            <!-- Alumno -->
            <template v-else-if="role==='student'">
              <Link :href="route('students.edit.me') + '#vacantes'" class="text-sm text-gray-700 dark:text-gray-200 hover:text-indigo-600">
                Vacantes
              </Link>
              <Link :href="route('students.edit.me') + '#matchings'" class="text-sm text-gray-700 dark:text-gray-200 hover:text-indigo-600">
                Matchings
              </Link>
            </template>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <Link :href="myEditorRoute" class="hidden md:inline-flex px-3 py-1.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
            Editar mi perfil
          </Link>

          <Link :href="myPublicRoute" class="text-sm text-gray-700 dark:text-gray-200 hover:text-indigo-600">
            {{ auth.user?.name }}
          </Link>
        </div>
      </div>
    </nav>

    <main>
      <slot />
    </main>
  </div>
</template>

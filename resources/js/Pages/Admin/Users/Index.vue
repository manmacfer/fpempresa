<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  users: { type: Array, required: true },
  type: { type: String, required: true }
})

const deleteUser = (user) => {
  const userName = user.first_name 
    ? `${user.first_name} ${user.last_name}` 
    : (user.company_name || user.name);
  
  if (confirm(`¿Estás seguro de eliminar a ${userName}? Esta acción no se puede deshacer y eliminará todos sus datos relacionados.`)) {
    router.delete(route('admin.users.destroy', { type: props.type, id: user.id }), {
      preserveScroll: true,
      onSuccess: () => {
        // El redirect se maneja desde el servidor
      }
    });
  }
}

const typeConfig = computed(() => {
  const configs = {
    student: {
      title: 'Alumnos',
      icon: '👨‍🎓',
      singular: 'alumno',
      color: 'blue'
    },
    company: {
      title: 'Empresas',
      icon: '🏢',
      singular: 'empresa',
      color: 'green'
    },
    teacher: {
      title: 'Profesores',
      icon: '👨‍🏫',
      singular: 'profesor',
      color: 'orange'
    }
  }
  return configs[props.type] || configs.student
})

const formatDate = (date) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Gestionar ${typeConfig.title}`" />

    <div class="mx-auto max-w-7xl p-6">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            {{ typeConfig.icon }} {{ typeConfig.title }}
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Total: {{ users.length }} {{ users.length === 1 ? typeConfig.singular : typeConfig.title.toLowerCase() }}
          </p>
        </div>
        <div class="flex gap-3">
          <Link 
            :href="route('admin.dashboard')" 
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
          >
            Volver
          </Link>
          <Link 
            :href="route('admin.users.create')" 
            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
          >
            + Crear Usuario
          </Link>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
          <Link
            :href="route('admin.users.index', { type: 'student' })"
            :class="[
              'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium',
              type === 'student'
                ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
            ]"
          >
            👨‍🎓 Alumnos
          </Link>
          <Link
            :href="route('admin.users.index', { type: 'company' })"
            :class="[
              'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium',
              type === 'company'
                ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
            ]"
          >
            🏢 Empresas
          </Link>
          <Link
            :href="route('admin.users.index', { type: 'teacher' })"
            :class="[
              'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium',
              type === 'teacher'
                ? 'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400'
                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'
            ]"
          >
            👨‍🏫 Profesores
          </Link>
        </nav>
      </div>

      <!-- Users List -->
      <div v-if="users.length === 0" class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-gray-900">
        <p class="text-gray-600 dark:text-gray-400">
          No hay {{ typeConfig.title.toLowerCase() }} registrados aún.
        </p>
        <Link
          :href="route('admin.users.create')"
          class="mt-4 inline-block rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
          Crear primer {{ typeConfig.singular }}
        </Link>
      </div>

      <div v-else class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th v-if="type === 'student'" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                Nombre
              </th>
              <th v-if="type === 'company'" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                Empresa
              </th>
              <th v-if="type === 'teacher'" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                Nombre
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                Email
              </th>
              <th v-if="type === 'student'" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                Ciclo
              </th>
              <th v-if="type === 'company'" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                Sector
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                Fecha Registro
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
              <!-- Nombre/Empresa -->
              <td class="whitespace-nowrap px-6 py-4">
                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  <template v-if="type === 'student'">
                    {{ user.first_name }} {{ user.last_name }}
                  </template>
                  <template v-else-if="type === 'company'">
                    {{ user.trade_name || user.name }}
                  </template>
                  <template v-else-if="type === 'teacher'">
                    {{ user.full_name }}
                  </template>
                </div>
                <div v-if="type === 'student' && user.center" class="text-xs text-gray-500 dark:text-gray-400">
                  {{ user.center }}
                </div>
              </td>

              <!-- Email -->
              <td class="whitespace-nowrap px-6 py-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                  {{ user.email }}
                </div>
              </td>

              <!-- Ciclo (solo alumnos) -->
              <td v-if="type === 'student'" class="whitespace-nowrap px-6 py-4">
                <span v-if="user.cycle" class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/30 dark:text-blue-200">
                  {{ user.cycle }}
                </span>
                <span v-else class="text-sm text-gray-400">—</span>
              </td>

              <!-- Sector (solo empresas) -->
              <td v-if="type === 'company'" class="whitespace-nowrap px-6 py-4">
                <span v-if="user.sector" class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-200">
                  {{ user.sector }}
                </span>
                <span v-else class="text-sm text-gray-400">—</span>
              </td>

              <!-- Fecha -->
              <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                {{ formatDate(user.created_at) }}
              </td>

              <!-- Acciones -->
              <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                <Link
                  :href="route('admin.users.edit', { type: type, id: user.id })"
                  class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                  Editar
                </Link>
                <button
                  @click="deleteUser(user)"
                  class="ml-4 text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                >
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

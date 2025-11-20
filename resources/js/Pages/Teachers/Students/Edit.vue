<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
  student: Object
})
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Editar perfil de ${student.first_name} ${student.last_name}`" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-8">
          <Link
            :href="route('teacher.students.index')"
            class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 mb-4"
          >
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Volver a Mis Alumnos
          </Link>

          <h1 class="text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
            Editar perfil de {{ student.first_name }} {{ student.last_name }}
          </h1>
          <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            Los profesores pueden ver y editar los perfiles de sus alumnos
          </p>
        </div>

        <!-- Redirigir a la página de edición real del alumno -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700/50 p-8">
          <div class="text-center">
            <svg class="mx-auto h-16 w-16 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            
            <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">
              Editor de Perfil
            </h2>
            
            <p class="mt-2 text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
              Para editar el perfil de este alumno, usa el formulario completo de edición.
            </p>

            <div class="mt-8 flex justify-center gap-4">
              <Link
                :href="route('students.edit.me')"
                method="get"
                as="a"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 text-base font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors duration-200"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Editar Perfil Completo
              </Link>

              <Link
                :href="route('students.public.show', student.id)"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors duration-200"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Ver Perfil Público
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

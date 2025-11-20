<script setup>
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
    <Head title="Esperando validación" />
    
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 text-center">
      <!-- Icono de reloj -->
      <div class="mx-auto w-20 h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mb-6">
        <svg class="w-10 h-10 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>

      <!-- Mensaje principal -->
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-3">
        Esperando validación
      </h1>
      
      <p class="text-gray-600 dark:text-gray-400 mb-6">
        Tu cuenta ha sido creada correctamente, pero necesita ser validada por tu profesor antes de que puedas acceder a la plataforma.
      </p>

      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <p class="text-sm text-blue-800 dark:text-blue-300">
          <strong>¿Qué hacer ahora?</strong><br>
          Por favor, contacta con tu profesor para que valide tu cuenta. Recibirás acceso una vez que tu profesor apruebe tu registro.
        </p>
      </div>

      <!-- Botón de cerrar sesión -->
      <Link
        :href="route('logout')"
        method="post"
        as="button"
        class="inline-flex items-center justify-center w-full px-4 py-3 bg-gray-900 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-800 dark:hover:bg-gray-600 transition"
      >
        Cerrar sesión
      </Link>
    </div>
  </div>
</template>

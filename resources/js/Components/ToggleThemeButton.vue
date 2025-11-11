<script setup>
import { isDark, toggleTheme } from '@/Composables/useTheme'
</script>

<template>
  <button
    type="button"
    @click="toggleTheme"
    class="inline-flex items-center justify-center h-9 w-9 rounded-lg
           border border-gray-300/70 bg-white/70 backdrop-blur
           hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700
           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 dark:focus:ring-gray-600"
    :aria-label="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
    :title="isDark ? 'Modo claro' : 'Modo oscuro'"
  >
    <!-- Si está oscuro, muestro SOL para pasar a claro -->
    <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
         viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
            d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/>
    </svg>

    <!-- Si está claro, muestro LUNA para pasar a oscuro -->
    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
         viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
            d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
    </svg>
  </button>
</template>

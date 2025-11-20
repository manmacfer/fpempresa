<script setup>
import { isDark, toggleTheme } from '@/Composables/useTheme'
</script>

<template>
  <button
    type="button"
    @click="toggleTheme"
    class="group relative inline-flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl
           border border-gray-200 bg-white shadow-sm transition-all duration-300
           hover:scale-110 hover:shadow-md
           dark:border-gray-700 dark:bg-gray-800
           focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
    :aria-label="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
    :title="isDark ? 'Modo claro' : 'Modo oscuro'"
  >
    <!-- Fondo animado con gradiente -->
    <span class="absolute inset-0 bg-gradient-to-br from-yellow-400 to-orange-500 opacity-0 transition-opacity duration-300 group-hover:opacity-10 dark:from-indigo-500 dark:to-purple-600"></span>
    
    <!-- Si está oscuro, muestro SOL para pasar a claro -->
    <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" 
         class="relative z-10 h-5 w-5 rotate-0 scale-100 text-yellow-500 transition-all duration-500 dark:text-yellow-400"
         viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 3v2m0 14v2m9-9h-2M5 12H3m15.364 6.364-1.414-1.414M7.05 7.05 5.636 5.636m12.728 0-1.414 1.414M7.05 16.95l-1.414 1.414M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/>
    </svg>

    <!-- Si está claro, muestro LUNA para pasar a oscuro -->
    <svg v-else xmlns="http://www.w3.org/2000/svg" 
         class="relative z-10 h-5 w-5 rotate-0 scale-100 text-indigo-600 transition-all duration-500 dark:text-indigo-400"
         viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
    </svg>
  </button>
</template>

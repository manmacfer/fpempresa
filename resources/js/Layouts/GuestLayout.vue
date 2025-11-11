<script setup>
import { Link } from '@inertiajs/vue3'
import ToggleThemeButton from '@/Components/ToggleThemeButton.vue'
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-950">
    <nav class="border-b bg-white/70 backdrop-blur dark:bg-gray-900/70 dark:border-gray-800">
      <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
        <Link href="/" class="font-semibold text-gray-900 dark:text-gray-100">FP Empresa</Link>
        <div class="flex items-center gap-2">
          <ToggleThemeButton />
          <Link href="/dashboard" class="text-sm text-gray-600 hover:underline dark:text-gray-300">
            Inicio
          </Link>
        </div>
      </div>
    </nav>

    <main>
      <slot />
    </main>
  </div>
</template>

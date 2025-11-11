<script setup>
const model = defineModel('checked')
</script>

<template>
  <input
    type="checkbox"
    v-model="model"
    class="rounded border-gray-300 text-primary-600 shadow-sm
           focus:ring-primary-500
           dark:border-gray-700 dark:bg-gray-800 dark:focus:ring-primary-400
           accent-primary-600 dark:accent-primary-500"
  />
</template>

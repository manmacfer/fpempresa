<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  active: { type: Boolean, default: false },
  href: { type: String, required: false },
  as: { type: String, default: 'a' },
  method: { type: String, default: 'get' },
})

const classes = computed(() =>
  props.active
    ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-500 text-start text-base font-medium ' +
      'text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 ' +
      'dark:bg-gray-800 dark:text-indigo-300 dark:border-indigo-400 dark:focus:bg-gray-800/80'
    : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium ' +
      'text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 ' +
      'focus:outline-none focus:text-gray-800 focus:bg-gray-50 ' +
      'dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70'
)
</script>

<template>
  <component
    :is="href ? Link : 'button'"
    :href="href"
    :method="method"
    class="w-full"
    :class="classes"
  >
    <slot />
  </component>
</template>

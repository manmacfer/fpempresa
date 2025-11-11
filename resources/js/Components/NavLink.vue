<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  href: { type: String, required: true },
  active: { type: Boolean, default: false },
})

const classes = computed(() =>
  props.active
    ? 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium ' +
      'text-gray-900 bg-gray-100 dark:text-gray-100 dark:bg-gray-800'
    : 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium ' +
      'text-gray-600 hover:text-gray-900 hover:bg-gray-50 ' +
      'dark:text-gray-300 dark:hover:text-gray-100 dark:hover:bg-gray-800/70'
)
</script>

<template>
  <Link :href="href" :class="classes">
    <slot />
  </Link>
</template>

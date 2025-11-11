<script setup>
const props = defineProps({
  for: String,
  value: String,
  class: { type: String, default: '' },
});
</script>

<template>
  <label
    :for="for"
    class="block font-medium text-sm text-gray-700 dark:text-gray-200"
    :class="props.class"
  >
    <span v-if="value">{{ value }}</span>
    <span v-else><slot /></span>
  </label>
</template>

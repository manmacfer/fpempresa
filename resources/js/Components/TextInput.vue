<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  modelValue: [String, Number],
  id: { type: String, default: null },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  autofocus: { type: Boolean, default: false },
  autocomplete: { type: String, default: null },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  class: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);
onMounted(() => { if (props.autofocus) input.value?.focus(); });

const updateValue = (e) => emit('update:modelValue', e.target.value);
</script>

<template>
  <input
    :id="id"
    :type="type"
    :value="modelValue"
    :placeholder="placeholder"
    :autocomplete="autocomplete"
    :autofocus="autofocus"
    :required="required"
    :disabled="disabled"
    @input="updateValue"
    ref="input"
    class="rounded-md shadow-sm w-full
           border-gray-300 focus:border-primary-500 focus:ring-primary-500
           bg-white text-gray-900 placeholder-gray-400
           dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400 dark:border-gray-700
           dark:focus:border-primary-400 dark:focus:ring-primary-400
           disabled:bg-gray-100 disabled:cursor-not-allowed
           dark:disabled:bg-gray-800/60
           "
    :class="props.class"
  />
</template>

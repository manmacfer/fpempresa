<script setup>
import { ref, watch, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Buscar municipio...' },
  required: { type: Boolean, default: false },
  error: { type: String, default: null },
})

const emit = defineEmits(['update:modelValue', 'update:province'])

const inputValue = ref(props.modelValue)
const suggestions = ref([])
const loading = ref(false)
const showSuggestions = ref(false)
const selectedProvince = ref('')

// Actualizar valor cuando cambia desde el padre
watch(() => props.modelValue, (newVal) => {
  inputValue.value = newVal
})

// Debounce para búsqueda
let searchTimeout = null
watch(inputValue, (newVal) => {
  emit('update:modelValue', newVal)
  
  if (searchTimeout) clearTimeout(searchTimeout)
  
  if (newVal.length < 2) {
    suggestions.value = []
    showSuggestions.value = false
    return
  }
  
  searchTimeout = setTimeout(() => {
    searchMunicipalities(newVal)
  }, 300)
})

async function searchMunicipalities(query) {
  loading.value = true
  try {
    const response = await axios.get('/api/municipalities/search', {
      params: { q: query, limit: 10 }
    })
    suggestions.value = response.data.data || []
    showSuggestions.value = suggestions.value.length > 0
  } catch (error) {
    console.error('Error searching municipalities:', error)
    suggestions.value = []
  } finally {
    loading.value = false
  }
}

function selectMunicipality(municipality) {
  inputValue.value = municipality.name
  selectedProvince.value = municipality.province_name
  emit('update:modelValue', municipality.name)
  emit('update:province', municipality.province_name)
  showSuggestions.value = false
}

function handleBlur() {
  // Retrasar el cierre para permitir clicks en las sugerencias
  setTimeout(() => {
    showSuggestions.value = false
  }, 200)
}
</script>

<template>
  <div class="relative">
    <input
      v-model="inputValue"
      type="text"
      :placeholder="placeholder"
      :required="required"
      class="w-full rounded-lg border border-gray-300 px-3 py-2 
             dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100
             focus:border-indigo-500 focus:ring-indigo-500"
      @focus="showSuggestions = suggestions.length > 0"
      @blur="handleBlur"
    />
    
    <div v-if="loading" class="absolute right-3 top-3">
      <svg class="h-5 w-5 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
    <div
      v-if="showSuggestions && suggestions.length > 0"
      class="absolute z-50 mt-1 w-full rounded-lg border border-gray-300 bg-white shadow-lg
             dark:border-gray-600 dark:bg-gray-800"
    >
      <ul class="max-h-60 overflow-y-auto">
        <li
          v-for="municipality in suggestions"
          :key="municipality.code"
          class="cursor-pointer px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700"
          @click="selectMunicipality(municipality)"
        >
          <div class="font-medium text-gray-900 dark:text-gray-100">
            {{ municipality.name }}
          </div>
          <div class="text-xs text-gray-500 dark:text-gray-400">
            {{ municipality.province_name }}
          </div>
        </li>
      </ul>
    </div>
    
    <div v-if="error" class="mt-1 text-xs text-red-600">{{ error }}</div>
  </div>
</template>

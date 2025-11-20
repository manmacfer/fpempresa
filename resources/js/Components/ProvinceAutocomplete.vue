<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Seleccionar provincia...' },
  required: { type: Boolean, default: false },
  error: { type: String, default: null },
})

const emit = defineEmits(['update:modelValue'])

const inputValue = ref(props.modelValue)
const provinces = ref([])
const filteredProvinces = ref([])
const loading = ref(false)
const showDropdown = ref(false)

// Cargar todas las provincias al montar
loadProvinces()

async function loadProvinces() {
  loading.value = true
  try {
    const response = await axios.get('/api/provinces')
    provinces.value = response.data.data || []
    filteredProvinces.value = provinces.value
  } catch (error) {
    console.error('Error loading provinces:', error)
  } finally {
    loading.value = false
  }
}

watch(() => props.modelValue, (newVal) => {
  inputValue.value = newVal
})

watch(inputValue, (newVal) => {
  emit('update:modelValue', newVal)
  
  // Filtrar provincias localmente
  if (newVal.length < 2) {
    filteredProvinces.value = provinces.value
  } else {
    const search = newVal.toLowerCase()
    filteredProvinces.value = provinces.value.filter(p => 
      p.name.toLowerCase().includes(search)
    )
  }
  showDropdown.value = filteredProvinces.value.length > 0
})

function selectProvince(province) {
  inputValue.value = province.name
  emit('update:modelValue', province.name)
  showDropdown.value = false
}

function handleBlur() {
  setTimeout(() => {
    showDropdown.value = false
  }, 200)
}

function handleFocus() {
  filteredProvinces.value = provinces.value
  showDropdown.value = true
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
      @focus="handleFocus"
      @blur="handleBlur"
    />
    
    <div v-if="loading" class="absolute right-3 top-3">
      <svg class="h-5 w-5 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
    
    <div
      v-if="showDropdown && filteredProvinces.length > 0"
      class="absolute z-50 mt-1 w-full rounded-lg border border-gray-300 bg-white shadow-lg
             dark:border-gray-600 dark:bg-gray-800"
    >
      <ul class="max-h-60 overflow-y-auto">
        <li
          v-for="province in filteredProvinces"
          :key="province.code"
          class="cursor-pointer px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700"
          @click="selectProvince(province)"
        >
          <div class="font-medium text-gray-900 dark:text-gray-100">
            {{ province.name }}
          </div>
        </li>
      </ul>
    </div>
    
    <div v-if="error" class="mt-1 text-xs text-red-600">{{ error }}</div>
  </div>
</template>

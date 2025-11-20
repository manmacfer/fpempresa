<template>
    <div class="relative" ref="containerRef">
        <div>
            <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                {{ label }}
            </label>
            <input
                ref="inputRef"
                v-model="searchQuery"
                type="text"
                :placeholder="placeholder"
                @input="handleInput"
                @focus="handleFocus"
                @blur="handleBlur"
                @keydown.down.prevent="navigateDown"
                @keydown.up.prevent="navigateUp"
                @keydown.enter.prevent="selectHighlighted"
                @keydown.esc="closeDropdown"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500
                       dark:bg-gray-800 dark:border-gray-700 dark:text-gray-100 dark:placeholder-gray-400
                       dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                :class="{ 'border-red-500 dark:border-red-500': error }"
            />
            <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ error }}</p>
        </div>

        <!-- Dropdown de resultados con Teleport -->
        <Teleport to="body">
            <div
                v-if="showDropdown && filteredMunicipalities.length > 0"
                :style="dropdownStyle"
                class="fixed z-[9999] bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto"
            >
                <div
                    v-for="(municipality, index) in filteredMunicipalities"
                    :key="municipality.id"
                    @mousedown.prevent="selectMunicipality(municipality)"
                    @mouseenter="highlightedIndex = index"
                    class="px-4 py-2 cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900/30"
                    :class="{
                        'bg-indigo-100 dark:bg-indigo-900/50': index === highlightedIndex,
                        'bg-white dark:bg-gray-800': index !== highlightedIndex
                    }"
                >
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ municipality.name }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ municipality.province_name }}
                    </div>
                </div>
            </div>

            <!-- Mensaje de carga -->
            <div
                v-if="loading"
                :style="dropdownStyle"
                class="fixed z-[9999] bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg px-4 py-3"
            >
                <p class="text-sm text-gray-500 dark:text-gray-400">Buscando...</p>
            </div>

            <!-- Sin resultados -->
            <div
                v-if="showDropdown && !loading && searchQuery.length >= 2 && filteredMunicipalities.length === 0"
                :style="dropdownStyle"
                class="fixed z-[9999] bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg px-4 py-3"
            >
                <p class="text-sm text-gray-500 dark:text-gray-400">No se encontraron municipios</p>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    modelValue: {
        type: Object,
        default: null
    },
    label: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: 'Buscar municipio...'
    },
    error: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue', 'select']);

const inputRef = ref(null);
const containerRef = ref(null);
const searchQuery = ref('');
const municipalities = ref([]);
const loading = ref(false);
const showDropdown = ref(false);
const highlightedIndex = ref(0);
const searchTimeout = ref(null);
const dropdownStyle = ref({});

// Si hay un valor inicial, mostrarlo
if (props.modelValue) {
    searchQuery.value = `${props.modelValue.name} (${props.modelValue.province_name})`;
}

const filteredMunicipalities = computed(() => municipalities.value);

// Calcular posición del dropdown
const updateDropdownPosition = () => {
    if (!inputRef.value) return;
    
    const rect = inputRef.value.getBoundingClientRect();
    dropdownStyle.value = {
        top: `${rect.bottom + 4}px`,
        left: `${rect.left}px`,
        width: `${rect.width}px`,
    };
};

const handleFocus = () => {
    showDropdown.value = true;
    updateDropdownPosition();
};

const handleInput = () => {
    highlightedIndex.value = 0;
    updateDropdownPosition();
    
    // Limpiar timeout anterior
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }
    
    // Si el query es muy corto, no buscar
    if (searchQuery.value.length < 2) {
        municipalities.value = [];
        showDropdown.value = false;
        return;
    }
    
    // Debounce: esperar 300ms antes de buscar
    searchTimeout.value = setTimeout(() => {
        searchMunicipalities();
    }, 300);
};

const searchMunicipalities = async () => {
    loading.value = true;
    showDropdown.value = true;
    updateDropdownPosition();
    
    try {
        const response = await axios.get('/api/municipalities/search', {
            params: { 
                q: searchQuery.value,
                limit: 20
            }
        });
        // La API devuelve { data: [...] }
        municipalities.value = response.data.data || [];
    } catch (error) {
        console.error('Error buscando municipios:', error);
        municipalities.value = [];
    } finally {
        loading.value = false;
    }
};

const selectMunicipality = (municipality) => {
    searchQuery.value = `${municipality.name} (${municipality.province_name})`;
    emit('update:modelValue', municipality);
    emit('select', municipality);
    closeDropdown();
};

const navigateDown = () => {
    if (highlightedIndex.value < filteredMunicipalities.value.length - 1) {
        highlightedIndex.value++;
    }
};

const navigateUp = () => {
    if (highlightedIndex.value > 0) {
        highlightedIndex.value--;
    }
};

const selectHighlighted = () => {
    if (filteredMunicipalities.value.length > 0 && highlightedIndex.value >= 0) {
        selectMunicipality(filteredMunicipalities.value[highlightedIndex.value]);
    }
};

const handleBlur = () => {
    // Delay para permitir click en dropdown
    setTimeout(() => {
        closeDropdown();
    }, 200);
};

const closeDropdown = () => {
    showDropdown.value = false;
    highlightedIndex.value = 0;
};

// Actualizar posición al hacer scroll o resize
const handleScrollResize = () => {
    if (showDropdown.value) {
        updateDropdownPosition();
    }
};

onMounted(() => {
    window.addEventListener('scroll', handleScrollResize, true);
    window.addEventListener('resize', handleScrollResize);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScrollResize, true);
    window.removeEventListener('resize', handleScrollResize);
});
</script>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
const props = defineProps({ company: Object })
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto p-6">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6 space-y-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
          {{ props.company.trade_name || props.company.legal_name || 'Empresa' }}
        </h1>
        <p class="text-gray-600 dark:text-gray-300" v-if="props.company.description">{{ props.company.description }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div v-if="props.company.sector"><span class="font-medium">Sector:</span> {{ props.company.sector }}</div>
          <div v-if="props.company.website"><span class="font-medium">Web:</span> <a :href="props.company.website" target="_blank" class="text-indigo-600 hover:underline">Visitar</a></div>
          <div v-if="props.company.linkedin"><span class="font-medium">LinkedIn:</span> <a :href="props.company.linkedin" target="_blank" class="text-indigo-600 hover:underline">Ver perfil</a></div>
          <div v-if="props.company.city || props.company.province">
            <span class="font-medium">Ubicación:</span> {{ props.company.city }} <span v-if="props.company.city && props.company.province">—</span> {{ props.company.province }}
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

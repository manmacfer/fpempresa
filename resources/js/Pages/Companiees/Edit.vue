<script setup>
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({ company: Object })

const form = useForm({
  legal_name: props.company?.legal_name ?? '',
  trade_name: props.company?.trade_name ?? '',
  cif: props.company?.cif ?? '',
  sector: props.company?.sector ?? '',
  website: props.company?.website ?? '',
  linkedin: props.company?.linkedin ?? '',
  phone: props.company?.phone ?? '',
  city: props.company?.city ?? '',
  province: props.company?.province ?? '',
  postal_code: props.company?.postal_code ?? '',
  contact_name: props.company?.contact_name ?? '',
  contact_email: props.company?.contact_email ?? '',
  description: props.company?.description ?? '',
})

function submit() {
  form.put(route('companies.update.me'))
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-4xl mx-auto p-6">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Perfil de Empresa</h1>

      <div class="bg-white dark:bg-gray-800 shadow rounded-2xl p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Nombre comercial</label>
            <input v-model="form.trade_name" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Nombre legal</label>
            <input v-model="form.legal_name" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
          </div>

          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">CIF</label>
            <input v-model="form.cif" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Sector</label>
            <input v-model="form.sector" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
          </div>

          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Web</label>
            <input type="url" v-model="form.website" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">LinkedIn</label>
            <input type="url" v-model="form.linkedin" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
          </div>

          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Teléfono</label>
            <input v-model="form.phone" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
          </div>
          <div class="grid grid-cols-3 gap-2">
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">CP</label>
              <input v-model="form.postal_code" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Ciudad</label>
              <input v-model="form.city" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Provincia</label>
              <input v-model="form.province" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>
          </div>

          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Contacto (nombre)</label>
            <input v-model="form.contact_name" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
          </div>
          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Contacto (email)</label>
            <input type="email" v-model="form.contact_email" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
          </div>
        </div>

        <div>
          <label class="block text-sm text-gray-600 dark:text-gray-300">Descripción breve</label>
          <textarea v-model="form.description" rows="5" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"></textarea>
        </div>

        <div class="flex items-center gap-3">
          <button @click="submit" :disabled="form.processing"
                  class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
            Guardar
          </button>
          <div v-if="form.recentlySuccessful" class="text-sm text-green-600">Guardado ✅</div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

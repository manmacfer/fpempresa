<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MunicipalitySearch from '@/Components/MunicipalitySearch.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  company: {
    type: Object,
    required: true,
  },
})

const previewUrl = ref(props.company.logo_url ?? null)

const form = useForm({
  logo: null,
  legal_name: props.company.legal_name ?? '',
  trade_name: props.company.trade_name ?? '',
  sector: props.company.sector ?? '',
  website: props.company.website ?? '',
  linkedin: props.company.linkedin ?? '',
  city: props.company.city ?? '',
  province: props.company.province ?? '',
  postal_code: props.company.postal_code ?? '',
  description: props.company.description ?? '',
  contact_name: props.company.contact_name ?? '',
  contact_email: props.company.contact_email ?? '',
})

function onPickLogo(e) {
  const f = e.target.files?.[0]
  form.logo = f || null
  if (f) {
    const r = new FileReader()
    r.onload = () => (previewUrl.value = r.result)
    r.readAsDataURL(f)
  }
}

function handleMunicipalitySelect(municipality) {
  form.city = municipality.name
  form.province = municipality.province_name
}

function submit() {
  form.post(route('companies.update.me'), {
    preserveScroll: true,
    forceFormData: true,
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
      <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Header con gradiente -->
        <div class="mb-8 overflow-hidden rounded-2xl border border-gray-100 bg-gradient-to-br from-indigo-600 to-purple-600 p-8 shadow-lg dark:border-gray-800 dark:from-indigo-700 dark:to-purple-700">
          <h1 class="text-3xl font-bold text-white">Editar perfil de empresa</h1>
          <p class="mt-2 text-indigo-100">Actualiza la información de tu empresa</p>
        </div>

        <!-- Logo -->
        <section class="mb-6 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
          <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
            <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
              <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Logo de la empresa
            </h3>
          </div>
          <div class="p-6">
            <div class="flex items-center gap-4">
              <img
                v-if="previewUrl"
                :src="previewUrl"
                alt="Logo"
                class="h-24 w-24 rounded-lg object-cover ring-1 ring-gray-200 dark:ring-gray-700"
              />
              <div class="flex-1">
                <input
                  type="file"
                  accept="image/*"
                  @change="onPickLogo"
                  class="text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/50"
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG o GIF. Máximo 2 MB.</p>
              </div>
            </div>
            <div v-if="form.errors.logo" class="mt-2 text-xs text-red-600">
              {{ form.errors.logo }}
            </div>
          </div>
          </section>

      <!-- Datos básicos -->
      <section class="mb-6 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
          <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Datos básicos
          </h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Nombre legal
            </label>
            <input
              v-model="form.legal_name"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.legal_name" class="mt-1 text-xs text-red-600">
              {{ form.errors.legal_name }}
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Nombre comercial
            </label>
            <input
              v-model="form.trade_name"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.trade_name" class="mt-1 text-xs text-red-600">
              {{ form.errors.trade_name }}
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Sector
            </label>
            <input
              v-model="form.sector"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.sector" class="mt-1 text-xs text-red-600">
              {{ form.errors.sector }}
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Persona de contacto
            </label>
            <input
              v-model="form.contact_name"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.contact_name" class="mt-1 text-xs text-red-600">
              {{ form.errors.contact_name }}
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Email de contacto
            </label>
            <input
              v-model="form.contact_email"
              type="email"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.contact_email" class="mt-1 text-xs text-red-600">
              {{ form.errors.contact_email }}
            </div>
          </div>
        </div>
        </div>
      </section>

      <!-- Ubicación -->
      <section class="mb-6 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
          <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Ubicación
          </h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div class="sm:col-span-2">
            <MunicipalitySearch
              label="Municipio y Provincia"
              placeholder="Escribe para buscar municipio..."
              @select="handleMunicipalitySelect"
              :error="form.errors.city || form.errors.province"
            />
            <div v-if="form.city && form.province" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
              Seleccionado: <strong>{{ form.city }}</strong> ({{ form.province }})
            </div>
          </div>

          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Código postal
            </label>
            <input
              v-model="form.postal_code"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.postal_code" class="mt-1 text-xs text-red-600">
              {{ form.errors.postal_code }}
            </div>
          </div>
        </div>
        </div>
      </section>

      <!-- Enlaces -->
      <section class="mb-6 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
          <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            Enlaces
          </h3>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 gap-5">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              Sitio web
            </label>
            <input
              v-model="form.website"
              type="url"
              placeholder="https://ejemplo.com"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.website" class="mt-1 text-xs text-red-600">
              {{ form.errors.website }}
            </div>
          </div>

          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
              LinkedIn
            </label>
            <input
              v-model="form.linkedin"
              type="url"
              placeholder="https://linkedin.com/company/..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.linkedin" class="mt-1 text-xs text-red-600">
              {{ form.errors.linkedin }}
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Ej: https://linkedin.com/company/mi-empresa
            </p>
          </div>
        </div>
        </div>
      </section>

      <!-- Descripción -->
      <section class="mb-6 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 dark:border-gray-800 dark:from-indigo-950/30 dark:to-purple-950/20">
          <h3 class="flex items-center gap-2 text-sm font-semibold uppercase tracking-wide text-gray-700 dark:text-gray-300">
            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            Sobre la empresa
          </h3>
        </div>
        <div class="p-6">
          <label class="mb-2 block text-sm font-semibold text-gray-700 dark:text-gray-300">
            Descripción
          </label>
          <textarea
            v-model="form.description"
            rows="6"
            placeholder="Cuéntanos sobre tu empresa, misión, valores..."
            class="w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm transition-all duration-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
          ></textarea>
          <div v-if="form.errors.description" class="mt-2 text-xs text-red-600 dark:text-red-400">
            {{ form.errors.description }}
          </div>
        </div>
      </section>

      <!-- Botones -->
      <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-800">
        <Link
          href="/companies/me"
          class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
        >
          Cancelar
        </Link>
        <button
          type="submit"
          @click="submit"
          :disabled="form.processing"
          class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:shadow-xl disabled:opacity-50 disabled:hover:scale-100 dark:from-indigo-500 dark:to-purple-500"
        >
          <svg v-if="!form.processing" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <svg v-else class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
        </button>
      </div>
    </div>
    </div>
  </AuthenticatedLayout>
</template>
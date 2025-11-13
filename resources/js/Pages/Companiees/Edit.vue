<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  company: {
    type: Object,
    required: true,
    // Esperado desde el controlador: id, legal_name, trade_name, cif, sector, website, linkedin,
    // phone, city, province, postal_code, contact_name, contact_email, description
  },
})

const page = usePage()
const flash = computed(() => page.props.flash || {})

const form = useForm({
  legal_name:   props.company?.legal_name   ?? '',
  trade_name:   props.company?.trade_name   ?? '',
  cif:          props.company?.cif          ?? '',
  sector:       props.company?.sector       ?? '',
  website:      props.company?.website      ?? '',
  linkedin:     props.company?.linkedin     ?? '',
  phone:        props.company?.phone        ?? '',
  city:         props.company?.city         ?? '',
  province:     props.company?.province     ?? '',
  postal_code:  props.company?.postal_code  ?? '',
  contact_name: props.company?.contact_name ?? '',
  contact_email:props.company?.contact_email?? '',
  description:  props.company?.description  ?? '',
})

function submit() {
  // Evitamos depender de Ziggy: URL directa
  form.put('/companies/me', {
    preserveScroll: true,
  })
}

const publicUrl = computed(() => `/companies/${props.company.id}`)
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-5xl mx-auto p-6">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Editar perfil de empresa</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400">Completa tu ficha pública para que los alumnos te conozcan.</p>
        </div>
        <div class="flex items-center gap-2">
          <Link :href="publicUrl"
                class="px-3 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50
                       dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">
            Ver perfil público
          </Link>
          <button @click="submit"
                  :disabled="form.processing"
                  class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60">
            Guardar
          </button>
        </div>
      </div>

      <div v-if="flash.success" class="mb-4 rounded-xl bg-green-50 text-green-800 px-4 py-3
                                       dark:bg-green-900/30 dark:text-green-200">
        {{ flash.success }}
      </div>
      <div v-if="Object.keys(form.errors).length"
           class="mb-4 rounded-xl bg-red-50 text-red-800 px-4 py-3
                  dark:bg-red-900/30 dark:text-red-200">
        <ul class="list-disc ml-5 space-y-1">
          <li v-for="(msg, key) in form.errors" :key="key">{{ msg }}</li>
        </ul>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna 1: Identidad -->
        <section class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-800 shadow p-6 space-y-5">
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Identidad</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Nombre comercial</label>
              <input v-model="form.trade_name" class="mt-1 w-full rounded-lg border-gray-300
                   dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Nombre legal</label>
              <input v-model="form.legal_name" class="mt-1 w-full rounded-lg border-gray-300
                   dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>

            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">CIF</label>
              <input v-model="form.cif" class="mt-1 w-full rounded-lg border-gray-300
                   dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Sector</label>
              <input v-model="form.sector" class="mt-1 w-full rounded-lg border-gray-300
                   dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>

            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Web</label>
              <input v-model="form.website" type="url" placeholder="https://..."
                     class="mt-1 w-full rounded-lg border-gray-300
                     dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">LinkedIn</label>
              <input v-model="form.linkedin" type="url" placeholder="https://linkedin.com/company/..."
                     class="mt-1 w-full rounded-lg border-gray-300
                     dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>
          </div>

          <div>
            <label class="block text-sm text-gray-600 dark:text-gray-300">Descripción</label>
            <textarea v-model="form.description" rows="5"
                      class="mt-1 w-full rounded-lg border-gray-300
                      dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"></textarea>
          </div>
        </section>

        <!-- Columna 2: Contacto -->
        <aside class="rounded-2xl bg-white dark:bg-gray-800 shadow p-6 space-y-5">
          <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Contacto</h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Teléfono</label>
              <input v-model="form.phone" class="mt-1 w-full rounded-lg border-gray-300
                   dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>

            <div class="grid grid-cols-3 gap-2">
              <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300">CP</label>
                <input v-model="form.postal_code" class="mt-1 w-full rounded-lg border-gray-300
                     dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
              </div>
              <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300">Ciudad</label>
                <input v-model="form.city" class="mt-1 w-full rounded-lg border-gray-300
                     dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
              </div>
              <div>
                <label class="block text-sm text-gray-600 dark:text-gray-300">Provincia</label>
                <input v-model="form.province" class="mt-1 w-full rounded-lg border-gray-300
                     dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
              </div>
            </div>

            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Persona de contacto</label>
              <input v-model="form.contact_name" class="mt-1 w-full rounded-lg border-gray-300
                   dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>

            <div>
              <label class="block text-sm text-gray-600 dark:text-gray-300">Email de contacto</label>
              <input v-model="form.contact_email" type="email" class="mt-1 w-full rounded-lg border-gray-300
                   dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"/>
            </div>
          </div>
        </aside>
      </div>

      <div class="flex justify-end mt-6">
        <button @click="submit"
                :disabled="form.processing"
                class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60">
          Guardar cambios
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

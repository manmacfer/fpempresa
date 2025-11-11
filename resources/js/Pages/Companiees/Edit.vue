<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({ company: { type: Object, required: true } })

const form = useForm({
  name: props.company?.name || '',
  city: props.company?.city || '',
  website: props.company?.website || '',
  description: props.company?.description || '',
  sectors: props.company?.sectors ? [...props.company.sectors] : [],
  avatar: null,
  newSector: '',
})

function addSector() {
  const v = (form.newSector || '').trim()
  if (!v) return
  form.sectors.push(v)
  form.newSector = ''
}
function removeSector(i) {
  form.sectors.splice(i, 1)
}

function submit() {
  const hasFile = !!form.avatar
  const req = form.transform(d => ({ ...d }))
  if (hasFile) req.post(route('companies.update.me'), { forceFormData: true })
  else req.post(route('companies.update.me'))
}
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Mi empresa</h2>
        <Link
          v-if="company?.id"
          :href="route('companies.public.show', company.id)"
          class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
        >
          Ver perfil público
        </Link>
      </div>
    </template>

    <div class="mx-auto max-w-5xl space-y-6 p-6">
      <!-- Datos básicos -->
      <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Datos básicos</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Nombre de la empresa</label>
            <input
              v-model="form.name"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900
                     dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</div>
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Ciudad</label>
            <input
              v-model="form.city"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900
                     dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div>
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Web</label>
            <input
              v-model="form.website"
              type="url"
              placeholder="https://..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900
                     dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
            <div v-if="form.errors.website" class="mt-1 text-xs text-red-600">{{ form.errors.website }}</div>
          </div>
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Descripción</label>
            <textarea
              v-model="form.description"
              rows="4"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900
                     dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
          <div class="sm:col-span-2">
            <label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Logo / Avatar</label>
            <input
              type="file"
              accept="image/png,image/jpeg"
              @change="e => form.avatar = e.target.files?.[0] || null"
              class="block w-full text-sm text-gray-700 file:me-3 file:rounded-md file:border-0 file:bg-gray-900 file:px-3 file:py-2 file:text-white hover:file:bg-gray-800 dark:text-gray-300 dark:file:bg-gray-100 dark:file:text-gray-900 dark:hover:file:bg-gray-200"
            />
            <p v-if="company?.avatar_url" class="mt-2 text-xs text-gray-500 dark:text-gray-400">
              Logo actual: <span class="underline">{{ company.avatar_url }}</span>
            </p>
          </div>
        </div>
      </section>

      <!-- Sectores -->
      <section class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Sectores</h3>
        <div class="flex flex-wrap gap-2">
          <input
            v-model="form.newSector"
            type="text"
            placeholder="Ej. Desarrollo de software"
            class="w-64 rounded-lg border border-gray-300 px-3 py-2 text-gray-900
                   dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
            @keyup.enter="addSector"
          />
          <button
            type="button"
            @click="addSector"
            class="rounded-lg bg-gray-900 px-3 py-2 text-sm text-white dark:bg-gray-100 dark:text-gray-900"
          >
            Añadir
          </button>
        </div>
        <div class="mt-2 flex flex-wrap gap-2">
          <span
            v-for="(s,i) in form.sectors"
            :key="`${s}-${i}`"
            class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-800 dark:bg-gray-800 dark:text-gray-200"
          >
            {{ s }}
            <button class="ms-2 text-gray-500" @click="removeSector(i)">✕</button>
          </span>
        </div>
      </section>

      <div class="flex items-center justify-end gap-3">
        <span v-if="$page.props.flash?.success" class="text-sm text-green-700 dark:text-green-400">
          {{ $page.props.flash.success }}
        </span>
        <button
          type="button"
          @click="submit"
          :disabled="form.processing"
          class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white disabled:opacity-50 dark:bg-indigo-500"
        >
          Guardar
        </button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

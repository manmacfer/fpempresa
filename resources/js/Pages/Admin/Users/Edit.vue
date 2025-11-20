<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  user: { type: Object, required: true },
  type: { type: String, required: true }
})

const form = useForm({
  email: props.user.email,
  password: '',
  password_confirmation: '',
  // Profesor
  full_name: props.user.full_name || '',
  // Alumno
  first_name: props.user.first_name || '',
  last_name: props.user.last_name || '',
  cycle: props.user.cycle || '',
  center: props.user.center || '',
  city: props.user.city || '',
  province: props.user.province || '',
  phone: props.user.phone || '',
  fp_modality: props.user.fp_modality || '',
  // Empresa
  name: props.user.name || '',
  trade_name: props.user.trade_name || '',
  legal_name: props.user.legal_name || '',
  sector: props.user.sector || '',
  website: props.user.website || '',
  contact_name: props.user.contact_name || '',
})

const typeLabels = {
  student: 'Alumno',
  company: 'Empresa',
  teacher: 'Profesor'
}

const submit = () => {
  form.put(route('admin.users.update', { type: props.type, id: props.user.id }))
}
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Editar ${typeLabels[type]}`" />

    <div class="mx-auto max-w-3xl p-6">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            Editar {{ typeLabels[type] }}
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Modifica los datos del {{ typeLabels[type].toLowerCase() }}
          </p>
        </div>
        <Link 
          :href="route('admin.users.index', { type: type })" 
          class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
        >
          Volver
        </Link>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
          <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
            Información Básica
          </h3>

          <div class="space-y-4">
            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Email
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              />
              <p v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ form.errors.email }}
              </p>
            </div>

            <!-- Campos según tipo -->
            <template v-if="type === 'teacher'">
              <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Nombre Completo
                </label>
                <input
                  id="full_name"
                  v-model="form.full_name"
                  type="text"
                  required
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                />
                <p v-if="form.errors.full_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.full_name }}
                </p>
              </div>
            </template>

            <template v-if="type === 'student'">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nombre
                  </label>
                  <input
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                  <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ form.errors.first_name }}
                  </p>
                </div>

                <div>
                  <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Apellidos
                  </label>
                  <input
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                  <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ form.errors.last_name }}
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="cycle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Ciclo Formativo
                  </label>
                  <input
                    id="cycle"
                    v-model="form.cycle"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>

                <div>
                  <label for="center" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Centro Educativo
                  </label>
                  <input
                    id="center"
                    v-model="form.center"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Ciudad
                  </label>
                  <input
                    id="city"
                    v-model="form.city"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>

                <div>
                  <label for="province" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Provincia
                  </label>
                  <input
                    id="province"
                    v-model="form.province"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Teléfono
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>

                <div>
                  <label for="fp_modality" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Modalidad FP
                  </label>
                  <select
                    id="fp_modality"
                    v-model="form.fp_modality"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  >
                    <option value="">Seleccionar...</option>
                    <option value="general">General</option>
                    <option value="dual">Dual</option>
                  </select>
                </div>
              </div>
            </template>

            <template v-if="type === 'company'">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Nombre de la Empresa
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  required
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.name }}
                </p>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="trade_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nombre Comercial
                  </label>
                  <input
                    id="trade_name"
                    v-model="form.trade_name"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>

                <div>
                  <label for="legal_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nombre Legal
                  </label>
                  <input
                    id="legal_name"
                    v-model="form.legal_name"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>
              </div>

              <div>
                <label for="sector" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Sector
                </label>
                <input
                  id="sector"
                  v-model="form.sector"
                  type="text"
                  required
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                />
                <p v-if="form.errors.sector" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ form.errors.sector }}
                </p>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Ciudad
                  </label>
                  <input
                    id="city"
                    v-model="form.city"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>

                <div>
                  <label for="province" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Provincia
                  </label>
                  <input
                    id="province"
                    v-model="form.province"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Teléfono
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>

                <div>
                  <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Sitio Web
                  </label>
                  <input
                    id="website"
                    v-model="form.website"
                    type="url"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                </div>
              </div>

              <div>
                <label for="contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Nombre de Contacto
                </label>
                <input
                  id="contact_name"
                  v-model="form.contact_name"
                  type="text"
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                />
              </div>
            </template>
          </div>
        </div>

        <!-- Cambiar Contraseña -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
          <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
            Cambiar Contraseña
          </h3>
          <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Deja estos campos vacíos si no deseas cambiar la contraseña
          </p>

          <div class="space-y-4">
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Nueva Contraseña
              </label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              />
              <p v-if="form.errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ form.errors.password }}
              </p>
            </div>

            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Confirmar Nueva Contraseña
              </label>
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              />
            </div>
          </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
          <Link
            :href="route('admin.users.index', { type: type })"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
          >
            Cancelar
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

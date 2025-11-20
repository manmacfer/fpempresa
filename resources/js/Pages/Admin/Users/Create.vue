<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

const props = defineProps({
  classrooms: {
    type: Array,
    default: () => []
  }
})

const selectedRole = ref('student')

// Formularios separados para cada rol
const studentForm = useForm({
  role: 'student',
  email: '',
  password: '',
  password_confirmation: '',
  first_name: '',
  last_name: '',
  cycle: '',
  center: '',
})

const companyForm = useForm({
  role: 'company',
  email: '',
  password: '',
  password_confirmation: '',
  company_name: '',
  trade_name: '',
  sector: '',
})

const teacherForm = useForm({
  role: 'teacher',
  email: '',
  password: '',
  password_confirmation: '',
  first_name: '',
  last_name: '',
  classroom_action: 'new',
  classroom_id: '',
  classroom_number: '',
  classroom_name: '',
})

const adminForm = useForm({
  role: 'admin',
  email: '',
  password: '',
  password_confirmation: '',
})

// Formulario activo según el rol seleccionado
const activeForm = computed(() => {
  switch(selectedRole.value) {
    case 'student': return studentForm
    case 'company': return companyForm
    case 'teacher': return teacherForm
    case 'admin': return adminForm
    default: return studentForm
  }
})

const submit = () => {
  activeForm.value.post(route('admin.users.store'))
}
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Crear Usuario" />

    <div class="mx-auto max-w-3xl p-6">
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
            Crear Nuevo Usuario
          </h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Selecciona el tipo de usuario y completa los datos requeridos
          </p>
        </div>
        <Link 
          :href="route('admin.dashboard')" 
          class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
        >
          Volver
        </Link>
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <!-- Role Selector -->
        <div class="mb-6">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
            Tipo de Usuario
          </label>
          <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <button
              type="button"
              @click="selectedRole = 'student'"
              :class="[
                'flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition',
                selectedRole === 'student'
                  ? 'border-indigo-600 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/30'
                  : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'
              ]"
            >
              <span class="text-2xl">👨‍🎓</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Alumno</span>
            </button>

            <button
              type="button"
              @click="selectedRole = 'company'"
              :class="[
                'flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition',
                selectedRole === 'company'
                  ? 'border-indigo-600 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/30'
                  : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'
              ]"
            >
              <span class="text-2xl">🏢</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Empresa</span>
            </button>

            <button
              type="button"
              @click="selectedRole = 'teacher'"
              :class="[
                'flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition',
                selectedRole === 'teacher'
                  ? 'border-indigo-600 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/30'
                  : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'
              ]"
            >
              <span class="text-2xl">👨‍🏫</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Profesor</span>
            </button>

            <button
              type="button"
              @click="selectedRole = 'admin'"
              :class="[
                'flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition',
                selectedRole === 'admin'
                  ? 'border-indigo-600 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/30'
                  : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600'
              ]"
            >
              <span class="text-2xl">⚙️</span>
              <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Admin</span>
            </button>
          </div>
        </div>

        <div class="space-y-4">
          <!-- Mostrar errores generales -->
          <div v-if="Object.keys(activeactiveForm.errors).length > 0" class="rounded-lg bg-red-50 border border-red-200 p-4 dark:bg-red-900/20 dark:border-red-800">
            <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">Errores en el formulario:</h3>
            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
              <li v-for="(error, field) in activeactiveForm.errors" :key="field">
                <strong>{{ field }}:</strong> {{ error }}
              </li>
            </ul>
          </div>

          <!-- Email (común para todos) -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Email *
            </label>
            <input
              id="email"
              v-model="activeForm.email"
              type="email"
              required
              class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
            />
            <p v-if="activeactiveForm.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ activeactiveForm.errors.email }}
            </p>
          </div>

          <!-- Campos específicos según rol -->
          <!-- PROFESOR -->
          <template v-if="selectedRole === 'teacher'">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Nombre *
                </label>
                <input
                  id="first_name"
                  v-model="activeForm.first_name"
                  type="text"
                  required
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                />
                <p v-if="activeForm.errors.first_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ activeForm.errors.first_name }}
                </p>
              </div>

              <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Apellidos *
                </label>
                <input
                  id="last_name"
                  v-model="activeForm.last_name"
                  type="text"
                  required
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                />
                <p v-if="activeactiveForm.errors.last_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ activeactiveForm.errors.last_name }}
                </p>
              </div>
            </div>

            <!-- Sección de Classroom -->
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
              <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">
                Configuración de Classroom
              </h3>

              <!-- Radio buttons para elegir acción -->
              <div class="mb-4 space-y-2">
                <label class="flex items-center gap-2">
                  <input
                    type="radio"
                    v-model="activeForm.classroom_action"
                    value="new"
                    class="text-indigo-600 focus:ring-indigo-500"
                  />
                  <span class="text-sm text-gray-700 dark:text-gray-300">Crear nuevo classroom</span>
                </label>
                <label class="flex items-center gap-2">
                  <input
                    type="radio"
                    v-model="activeForm.classroom_action"
                    value="existing"
                    class="text-indigo-600 focus:ring-indigo-500"
                  />
                  <span class="text-sm text-gray-700 dark:text-gray-300">Asignar a classroom existente</span>
                </label>
              </div>

              <!-- Campos según la acción seleccionada -->
              <div v-if="activeForm.classroom_action === 'new'" class="space-y-3">
                <div>
                  <label for="classroom_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Número de Classroom *
                  </label>
                  <input
                    id="classroom_number"
                    v-model="activeForm.classroom_number"
                    type="text"
                    required
                    placeholder="Ej: 101, 202, etc."
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                  <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Este número será usado por los alumnos para registrarse
                  </p>
                  <p v-if="activeactiveForm.errors.classroom_number" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ activeactiveForm.errors.classroom_number }}
                  </p>
                </div>

                <div>
                  <label for="classroom_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nombre del Classroom *
                  </label>
                  <input
                    id="classroom_name"
                    v-model="activeForm.classroom_name"
                    type="text"
                    required
                    placeholder="Ej: 2º DAW A, 1º DAM B, etc."
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                  />
                  <p v-if="activeactiveForm.errors.classroom_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ activeactiveForm.errors.classroom_name }}
                  </p>
                </div>
              </div>

              <div v-else-if="activeForm.classroom_action === 'existing'">
                <label for="classroom_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Seleccionar Classroom *
                </label>
                <select
                  id="classroom_id"
                  v-model="activeForm.classroom_id"
                  required
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                >
                  <option value="">Selecciona un classroom...</option>
                  <option v-for="classroom in classrooms" :key="classroom.id" :value="classroom.id">
                    {{ classroom.classroom_number }} - {{ classroom.nombre }}
                  </option>
                </select>
                <p v-if="activeactiveForm.errors.classroom_id" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ activeactiveForm.errors.classroom_id }}
                </p>
              </div>
            </div>
          </template>

          <!-- ALUMNO -->
          <template v-if="selectedRole === 'student'">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Nombre *
                </label>
                <input
                  id="first_name"
                  v-model="activeForm.first_name"
                  type="text"
                  required
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                />
                <p v-if="activeForm.errors.first_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ activeForm.errors.first_name }}
                </p>
              </div>

              <div>
                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Apellidos *
                </label>
                <input
                  id="last_name"
                  v-model="activeForm.last_name"
                  type="text"
                  required
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                />
                <p v-if="activeForm.errors.last_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                  {{ activeForm.errors.last_name }}
                </p>
              </div>
            </div>

            <div>
              <label for="cycle" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Ciclo Formativo *
              </label>
              <input
                id="cycle"
                v-model="activeForm.cycle"
                type="text"
                required
                placeholder="Ej: 2 DAW"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              />
              <p v-if="activeForm.errors.cycle" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ activeForm.errors.cycle }}
              </p>
            </div>

            <div>
              <label for="center" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Centro Educativo *
              </label>
              <input
                id="center"
                v-model="activeForm.center"
                type="text"
                required
                placeholder="Ej: IES Velázquez"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              />
              <p v-if="activeForm.errors.center" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ activeForm.errors.center }}
              </p>
            </div>
          </template>

          <!-- EMPRESA -->
          <template v-if="selectedRole === 'company'">
            <div>
              <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Nombre de la Empresa *
              </label>
              <input
                id="company_name"
                v-model="activeForm.company_name"
                type="text"
                required
                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              />
              <p v-if="activeForm.errors.company_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ activeForm.errors.company_name }}
              </p>
            </div>

            <div>
              <label for="trade_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Nombre Comercial
              </label>
              <input
                id="trade_name"
                v-model="activeForm.trade_name"
                type="text"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              />
              <p v-if="activeForm.errors.trade_name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ activeForm.errors.trade_name }}
              </p>
            </div>

            <div>
              <label for="sector" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Sector *
              </label>
              <input
                id="sector"
                v-model="activeForm.sector"
                type="text"
                required
                placeholder="Ej: Tecnología, Salud, Educación..."
                class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
              />
              <p v-if="activeForm.errors.sector" class="mt-1 text-sm text-red-600 dark:text-red-400">
                {{ activeForm.errors.sector }}
              </p>
            </div>
          </template>

          <!-- Contraseña (común para todos) -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Contraseña *
            </label>
            <input
              id="password"
              v-model="activeForm.password"
              type="password"
              required
              class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
            />
            <p v-if="activeForm.errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">
              {{ activeForm.errors.password }}
            </p>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Confirmar Contraseña *
            </label>
            <input
              id="password_confirmation"
              v-model="activeForm.password_confirmation"
              type="password"
              required
              class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
            />
          </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-end gap-3">
          <Link
            :href="route('admin.dashboard')"
            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
          >
            Cancelar
          </Link>
          <button
            type="submit"
            :disabled="activeForm.processing"
            class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ activeForm.processing ? 'Creando...' : 'Crear Usuario' }}
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>


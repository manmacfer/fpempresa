<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const active = ref('student')

const form = useForm({
  // comunes
  email: '',
  password: '',
  password_confirmation: '',
  role: 'student',

  // alumno
  first_name: '',
  last_name: '',
  cycle: '',

  // empresa
  company_name: '',
  name: '', // fallback por si quieres usar un solo campo
})

function switchTo(r) {
  active.value = r
  form.role = r
}

function submit() {
  form.post('/register')
}
</script>

<template>
  <Head title="Registro" />

  <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-md bg-white dark:bg-gray-800 shadow rounded-2xl p-6">
      <div class="flex mb-4 rounded-xl overflow-hidden">
        <button @click="switchTo('student')"
                class="flex-1 py-2 text-center"
                :class="active==='student' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 dark:text-gray-200'">
          Alumno
        </button>
        <button @click="switchTo('company')"
                class="flex-1 py-2 text-center"
                :class="active==='company' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 dark:text-gray-200'">
          Empresa
        </button>
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <input v-model="form.role" type="hidden" name="role" />

        <!-- Campos por rol -->
        <div v-if="active==='student'" class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre</label>
            <input v-model="form.first_name" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Apellidos</label>
            <input v-model="form.last_name" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Ciclo</label>
            <select v-model="form.cycle" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required>
              <option value="" disabled>Selecciona</option>
              <option value="1 DAM">1 DAM</option>
              <option value="2 DAM">2 DAM</option>
              <option value="1 DAW">1 DAW</option>
              <option value="2 DAW">2 DAW</option>
            </select>
          </div>
        </div>

        <div v-else class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre de la empresa</label>
            <input v-model="form.company_name" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required />
          </div>
        </div>

        <!-- Comunes -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
          <input v-model="form.email" type="email" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contraseña</label>
          <input v-model="form.password" type="password" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Repite contraseña</label>
          <input v-model="form.password_confirmation" type="password" class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required />
        </div>

        <div v-if="form.errors" class="text-sm text-red-600">
          <div v-for="(e,k) in form.errors" :key="k">{{ e }}</div>
        </div>

        <button :disabled="form.processing" class="w-full py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
          Crear cuenta
        </button>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400">
          ¿Ya tienes cuenta?
          <Link href="/login" class="text-indigo-600 hover:underline">Entrar</Link>
        </p>
      </form>
    </div>
  </div>
</template>

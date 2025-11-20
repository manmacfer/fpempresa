<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  classrooms: { type: Array, default: () => [] }
})

const selectedRole = ref('student')

// Formularios separados
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

const submitStudent = () => studentForm.post(route('admin.users.store'))
const submitCompany = () => companyForm.post(route('admin.users.store'))
const submitTeacher = () => teacherForm.post(route('admin.users.store'))
const submitAdmin = () => adminForm.post(route('admin.users.store'))
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Crear Usuario" />

    <div class="mx-auto max-w-3xl p-6">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Crear Nuevo Usuario</h1>
          <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Selecciona el tipo de usuario y completa los datos</p>
        </div>
        <Link :href="route('admin.dashboard')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
          Volver
        </Link>
      </div>

      <!-- Role Selector -->
      <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-4">
        <button type="button" @click="selectedRole = 'student'" :class="['flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition', selectedRole === 'student' ? 'border-indigo-600 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/30' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600']">
          <span class="text-2xl">👨‍🎓</span>
          <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Alumno</span>
        </button>
        <button type="button" @click="selectedRole = 'company'" :class="['flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition', selectedRole === 'company' ? 'border-indigo-600 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/30' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600']">
          <span class="text-2xl">🏢</span>
          <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Empresa</span>
        </button>
        <button type="button" @click="selectedRole = 'teacher'" :class="['flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition', selectedRole === 'teacher' ? 'border-indigo-600 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/30' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600']">
          <span class="text-2xl">👨‍🏫</span>
          <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Profesor</span>
        </button>
        <button type="button" @click="selectedRole = 'admin'" :class="['flex flex-col items-center gap-2 rounded-lg border-2 p-4 transition', selectedRole === 'admin' ? 'border-indigo-600 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-900/30' : 'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600']">
          <span class="text-2xl">⚙️</span>
          <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Admin</span>
        </button>
      </div>

      <!-- FORM ALUMNO -->
      <form v-if="selectedRole === 'student'" @submit.prevent="submitStudent" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-4">
        <div v-if="Object.keys(studentForm.errors).length" class="rounded-lg bg-red-50 border border-red-200 p-4 dark:bg-red-900/20 dark:border-red-800">
          <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">Errores:</h3>
          <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
            <li v-for="(error, field) in studentForm.errors" :key="field"><strong>{{ field }}:</strong> {{ error }}</li>
          </ul>
        </div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label><input v-model="studentForm.email" type="email" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre *</label><input v-model="studentForm.first_name" type="text" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
          <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos *</label><input v-model="studentForm.last_name" type="text" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        </div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciclo *</label><input v-model="studentForm.cycle" type="text" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Centro *</label><input v-model="studentForm.center" type="text" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña *</label><input v-model="studentForm.password" type="password" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar *</label><input v-model="studentForm.password_confirmation" type="password" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div class="flex justify-end gap-3">
          <Link :href="route('admin.dashboard')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">Cancelar</Link>
          <button type="submit" :disabled="studentForm.processing" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">{{ studentForm.processing ? 'Creando...' : 'Crear Usuario' }}</button>
        </div>
      </form>

      <!-- FORM EMPRESA -->
      <form v-if="selectedRole === 'company'" @submit.prevent="submitCompany" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-4">
        <div v-if="Object.keys(companyForm.errors).length" class="rounded-lg bg-red-50 border border-red-200 p-4 dark:bg-red-900/20 dark:border-red-800">
          <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">Errores:</h3>
          <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
            <li v-for="(error, field) in companyForm.errors" :key="field"><strong>{{ field }}:</strong> {{ error }}</li>
          </ul>
        </div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label><input v-model="companyForm.email" type="email" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Empresa *</label><input v-model="companyForm.company_name" type="text" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre Comercial</label><input v-model="companyForm.trade_name" type="text" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sector *</label><input v-model="companyForm.sector" type="text" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña *</label><input v-model="companyForm.password" type="password" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar *</label><input v-model="companyForm.password_confirmation" type="password" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div class="flex justify-end gap-3">
          <Link :href="route('admin.dashboard')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">Cancelar</Link>
          <button type="submit" :disabled="companyForm.processing" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">{{ companyForm.processing ? 'Creando...' : 'Crear Usuario' }}</button>
        </div>
      </form>

      <!-- FORM PROFESOR -->
      <form v-if="selectedRole === 'teacher'" @submit.prevent="submitTeacher" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-4">
        <div v-if="Object.keys(teacherForm.errors).length" class="rounded-lg bg-red-50 border border-red-200 p-4 dark:bg-red-900/20 dark:border-red-800">
          <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">Errores:</h3>
          <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
            <li v-for="(error, field) in teacherForm.errors" :key="field"><strong>{{ field }}:</strong> {{ error }}</li>
          </ul>
        </div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label><input v-model="teacherForm.email" type="email" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div class="grid grid-cols-2 gap-4">
          <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre *</label><input v-model="teacherForm.first_name" type="text" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
          <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Apellidos *</label><input v-model="teacherForm.last_name" type="text" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        </div>
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
          <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Classroom</h3>
          <div class="mb-4 space-y-2">
            <label class="flex items-center gap-2"><input type="radio" v-model="teacherForm.classroom_action" value="new" class="text-indigo-600 focus:ring-indigo-500" /><span class="text-sm text-gray-700 dark:text-gray-300">Crear nuevo</span></label>
            <label class="flex items-center gap-2"><input type="radio" v-model="teacherForm.classroom_action" value="existing" class="text-indigo-600 focus:ring-indigo-500" /><span class="text-sm text-gray-700 dark:text-gray-300">Asignar existente</span></label>
          </div>
          <div v-if="teacherForm.classroom_action === 'new'" class="space-y-3">
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número *</label><input v-model="teacherForm.classroom_number" type="text" required placeholder="Ej: 101" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /><p class="mt-1 text-xs text-gray-500">Usado por alumnos para registrarse</p></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre *</label><input v-model="teacherForm.classroom_name" type="text" required placeholder="Ej: 2º DAW A" class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
          </div>
          <div v-else><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Seleccionar *</label><select v-model="teacherForm.classroom_id" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"><option value="">Selecciona...</option><option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.classroom_number }} - {{ c.nombre }}</option></select></div>
        </div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña *</label><input v-model="teacherForm.password" type="password" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar *</label><input v-model="teacherForm.password_confirmation" type="password" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div class="flex justify-end gap-3">
          <Link :href="route('admin.dashboard')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">Cancelar</Link>
          <button type="submit" :disabled="teacherForm.processing" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">{{ teacherForm.processing ? 'Creando...' : 'Crear Usuario' }}</button>
        </div>
      </form>

      <!-- FORM ADMIN -->
      <form v-if="selectedRole === 'admin'" @submit.prevent="submitAdmin" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-4">
        <div v-if="Object.keys(adminForm.errors).length" class="rounded-lg bg-red-50 border border-red-200 p-4 dark:bg-red-900/20 dark:border-red-800">
          <h3 class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2">Errores:</h3>
          <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400 space-y-1">
            <li v-for="(error, field) in adminForm.errors" :key="field"><strong>{{ field }}:</strong> {{ error }}</li>
          </ul>
        </div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label><input v-model="adminForm.email" type="email" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña *</label><input v-model="adminForm.password" type="password" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar *</label><input v-model="adminForm.password_confirmation" type="password" required class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" /></div>
        <div class="flex justify-end gap-3">
          <Link :href="route('admin.dashboard')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">Cancelar</Link>
          <button type="submit" :disabled="adminForm.processing" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">{{ adminForm.processing ? 'Creando...' : 'Crear Usuario' }}</button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

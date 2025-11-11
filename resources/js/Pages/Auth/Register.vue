<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'


const tab = ref('student') // 'student' | 'company'


const form = useForm({
role: 'student',
// alumno
first_name: '',
last_name: '',
cycle: '',
// empresa
company_name: '',
// comunes
email: '',
password: '',
password_confirmation: '',
})


function switchTab(next){
tab.value = next
form.role = next
}


function submit(){
form.post(route('register'))
}
</script>


<template>
<GuestLayout>
<Head title="Registrarse" />


<div class="mx-auto w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
<h1 class="mb-4 text-center text-xl font-semibold text-gray-900 dark:text-gray-100">Crear cuenta</h1>


<!-- Tabs Alumno / Empresa -->
<div class="mb-5 grid grid-cols-2 gap-1 rounded-xl bg-gray-100 p-1 dark:bg-gray-800">
<button type="button" @click="switchTab('student')"
:class="['rounded-lg px-3 py-2 text-sm font-medium', tab==='student' ? 'bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-300']">
Alumno
</button>
<button type="button" @click="switchTab('company')"
:class="['rounded-lg px-3 py-2 text-sm font-medium', tab==='company' ? 'bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100' : 'text-gray-600 dark:text-gray-300']">
Empresa
</button>
</div>

<!-- Formulario -->
<form @submit.prevent="submit" class="space-y-3">
<input type="hidden" v-model="form.role" />


<!-- Alumno -->
<template v-if="tab==='student'">
<div>
<label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Nombre</label>
<input v-model="form.first_name" type="text" autocomplete="given-name"
class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
<div v-if="form.errors.first_name" class="mt-1 text-xs text-red-600">{{ form.errors.first_name }}</div>
</div>
<div>
<label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Apellidos</label>
<input v-model="form.last_name" type="text" autocomplete="family-name"
class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
<div v-if="form.errors.last_name" class="mt-1 text-xs text-red-600">{{ form.errors.last_name }}</div>
</div>
<div>
<label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Ciclo</label>
<select v-model="form.cycle" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
<option value="">— Selecciona —</option>
<option value="1 DAM">1 DAM</option>
<option value="2 DAM">2 DAM</option>
<option value="1 DAW">1 DAW</option>
<option value="2 DAW">2 DAW</option>
</select>
<div v-if="form.errors.cycle" class="mt-1 text-xs text-red-600">{{ form.errors.cycle }}</div>
</div>
</template>


<!-- Empresa -->
<template v-else>
<div>
<label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Nombre de la empresa</label>
<input v-model="form.company_name" type="text" autocomplete="organization"
class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
<div v-if="form.errors.company_name" class="mt-1 text-xs text-red-600">{{ form.errors.company_name }}</div>
</div>
</template>


<div>
<label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Email</label>
<input v-model="form.email" type="email" autocomplete="email"
class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
<div v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</div>
</div>


<div>
<label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Contraseña</label>
<input v-model="form.password" type="password" autocomplete="new-password"
class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
<div v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</div>
</div>


<div>
<label class="mb-1 block text-sm text-gray-600 dark:text-gray-300">Confirmar contraseña</label>
<input v-model="form.password_confirmation" type="password" autocomplete="new-password"
class="w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100" />
</div>


<button type="submit" :disabled="form.processing"
class="mt-2 w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white disabled:opacity-50 dark:bg-indigo-500">
Crear cuenta
</button>


<p class="mt-3 text-center text-sm text-gray-600 dark:text-gray-300">
¿Ya tienes cuenta?
<Link :href="route('login')" class="text-indigo-600 hover:underline dark:text-indigo-400">Inicia sesión</Link>
</p>
</form>
</div>
</GuestLayout>
</template>

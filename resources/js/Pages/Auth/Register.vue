<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import ToggleThemeButton from '@/Components/ToggleThemeButton.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
  first_name: '',
  last_name: '',
  cycle: '',
  email: '',
  password: '',
  password_confirmation: '',
})

function submit() {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}

function fieldClass(field) {
  const base = 'block w-full rounded-xl border-0 px-3 py-2 ring-1 transition focus:outline-none text-sm'
  const ok   = 'ring-gray-300 focus:ring-2 focus:ring-indigo-500 dark:ring-slate-700 dark:bg-slate-800/60 dark:text-slate-100 dark:placeholder:text-slate-400'
  const err  = 'ring-red-500 focus:ring-2 focus:ring-red-500 dark:ring-red-500'
  return `${base} ${form.errors[field] ? err : ok}`
}
</script>

<template>
  <GuestLayout>
    <Head title="Crear cuenta" />

    <!-- Fondo bonito -->
    <div class="relative min-h-[90vh] overflow-hidden">
      <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-indigo-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-900"></div>
      <!-- blobs suaves -->
      <div class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-indigo-200/40 blur-3xl dark:bg-indigo-500/10"></div>
      <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-fuchsia-200/40 blur-3xl dark:bg-fuchsia-500/10"></div>

      <!-- Toggle tema -->
      <div class="absolute right-4 top-4 z-10">
        <ToggleThemeButton />
      </div>

      <!-- Card -->
      <div class="relative z-0 mx-auto mt-14 max-w-xl px-4 pb-16 sm:px-6 lg:px-8">
        <div class="mb-6 text-center">
          <div class="inline-flex items-center gap-2">
            <span class="rounded-xl bg-indigo-600/10 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300">FP Empresa</span>
          </div>
          <h1 class="mt-3 text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
            Crear cuenta
          </h1>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Regístrate con tus datos básicos. Solo pedimos lo imprescindible.
          </p>
        </div>

        <div class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 shadow-xl backdrop-blur supports-[backdrop-filter]:bg-white/65 dark:border-slate-800 dark:bg-slate-900/70">
          <form @submit.prevent="submit" class="space-y-5">
            <!-- Nombre y apellidos -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Nombre</label>
                <input v-model="form.first_name" :class="fieldClass('first_name')" type="text" autocomplete="given-name" placeholder="Nombre" />
                <p v-if="form.errors.first_name" class="mt-1 text-xs text-red-600">{{ form.errors.first_name }}</p>
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Apellidos</label>
                <input v-model="form.last_name" :class="fieldClass('last_name')" type="text" autocomplete="family-name" placeholder="Apellidos" />
                <p v-if="form.errors.last_name" class="mt-1 text-xs text-red-600">{{ form.errors.last_name }}</p>
              </div>
            </div>

            <!-- Ciclo -->
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Ciclo</label>
              <div class="relative">
                <select v-model="form.cycle" :class="fieldClass('cycle')">
                  <option value="">— Selecciona tu ciclo —</option>
                  <option value="1 DAM">1 DAM</option>
                  <option value="2 DAM">2 DAM</option>
                  <option value="1 DAW">1 DAW</option>
                  <option value="2 DAW">2 DAW</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">▾</div>
              </div>
              <p v-if="form.errors.cycle" class="mt-1 text-xs text-red-600">{{ form.errors.cycle }}</p>
            </div>

            <!-- Email -->
            <div>
              <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
              <input v-model="form.email" :class="fieldClass('email')" type="email" autocomplete="email" placeholder="nombre@centro.es" />
              <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
            </div>

            <!-- Passwords -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Contraseña</label>
                <input v-model="form.password" :class="fieldClass('password')" type="password" autocomplete="new-password" placeholder="••••••••" />
                <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
              </div>
              <div>
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Confirmar</label>
                <input v-model="form.password_confirmation" :class="fieldClass('password_confirmation')" type="password" autocomplete="new-password" placeholder="••••••••" />
              </div>
            </div>

            <!-- CTA -->
            <div class="flex items-center justify-between pt-2">
              <Link :href="route('login')" class="text-sm font-medium text-indigo-700 hover:underline dark:text-indigo-300">
                ¿Ya tienes cuenta? Inicia sesión
              </Link>

              <button
                :disabled="form.processing"
                class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-indigo-600 to-fuchsia-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:brightness-110 active:scale-[0.99] disabled:opacity-50 dark:from-indigo-500 dark:to-fuchsia-500"
              >
                Crear cuenta
              </button>
            </div>
          </form>
        </div>

        <!-- Nota de privacidad / pie -->
        <p class="mt-4 text-center text-xs text-slate-500 dark:text-slate-400">
          Al crear tu cuenta aceptas el tratamiento de tus datos para la gestión de prácticas.
        </p>
      </div>
    </div>
  </GuestLayout>
</template>

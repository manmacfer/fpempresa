<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Crear cuenta" />

    <div class="min-h-[70vh] flex items-center justify-center p-6">
      <div class="w-full max-w-md">
        <!-- Tarjeta -->
        <div
          class="rounded-2xl shadow-lg p-8 border
                 bg-white/90 border-gray-100 text-gray-900
                 dark:bg-gray-900/80 dark:border-gray-800 dark:text-gray-100"
        >
          <h1 class="text-2xl font-semibold mb-2">Crear cuenta</h1>
          <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
            ¿Ya tienes cuenta?
            <Link :href="route('login')" class="text-primary-600 hover:underline dark:text-primary-400">
              Inicia sesión
            </Link>
          </p>

          <form @submit.prevent="submit" class="space-y-5">
            <!-- Nombre -->
            <div>
              <InputLabel for="name" value="Nombre completo" />
              <TextInput
                id="name"
                type="text"
                class="mt-1"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
                placeholder="Tu nombre"
              />
              <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Email -->
            <div>
              <InputLabel for="email" value="Correo electrónico" />
              <TextInput
                id="email"
                type="email"
                class="mt-1"
                v-model="form.email"
                required
                autocomplete="username"
                placeholder="tucorreo@ejemplo.com"
              />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div>
              <InputLabel for="password" value="Contraseña" />
              <TextInput
                id="password"
                type="password"
                class="mt-1"
                v-model="form.password"
                required
                autocomplete="new-password"
                placeholder="••••••••"
              />
              <InputError class="mt-2" :message="form.errors.password" />
              <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Usa al menos 8 caracteres. Combina letras y números si puedes.
              </p>
            </div>

            <!-- Confirmación -->
            <div>
              <InputLabel for="password_confirmation" value="Confirmar contraseña" />
              <TextInput
                id="password_confirmation"
                type="password"
                class="mt-1"
                v-model="form.password_confirmation"
                required
                autocomplete="new-password"
                placeholder="••••••••"
              />
              <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- CTA -->
            <PrimaryButton
              class="w-full justify-center"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              Crear cuenta
            </PrimaryButton>

            <!-- Enlace a login (extra en el pie, opcional) -->
            <p class="text-sm text-gray-600 text-center dark:text-gray-300">
              ¿Ya tienes cuenta?
              <Link :href="route('login')" class="text-primary-600 hover:underline dark:text-primary-400">
                Inicia sesión
              </Link>
            </p>
          </form>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

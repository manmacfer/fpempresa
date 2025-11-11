<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps({
  status: { type: String },
});

const form = useForm({
  email: '',
});

const submit = () => {
  form.post(route('password.email'));
};
</script>

<template>
  <GuestLayout>
    <Head title="Recuperar contraseña" />

    <div class="min-h-[70vh] flex items-center justify-center p-6">
      <div class="w-full max-w-md">
        <div
          class="rounded-2xl shadow-lg p-8 border
                 bg-white/90 border-gray-100 text-gray-900
                 dark:bg-gray-900/80 dark:border-gray-800 dark:text-gray-100"
        >
          <h1 class="text-2xl font-semibold mb-2">¿Olvidaste tu contraseña?</h1>
          <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">
            No pasa nada. Indícanos tu correo y te enviaremos un enlace para que puedas
            <span class="font-medium">restablecer tu contraseña</span>.
          </p>

          <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
          </div>

          <form @submit.prevent="submit" class="space-y-5">
            <div>
              <InputLabel for="email" value="Correo electrónico" />
              <TextInput
                id="email"
                type="email"
                class="mt-1"
                v-model="form.email"
                required
                autofocus
                autocomplete="username"
                placeholder="tucorreo@ejemplo.com"
              />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <PrimaryButton
              class="w-full justify-center"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              Enviar enlace de recuperación
            </PrimaryButton>

            <p class="text-sm text-gray-600 text-center dark:text-gray-300">
              ¿La recordaste?
              <Link :href="route('login')" class="text-indigo-600 hover:underline dark:text-indigo-400">
                Volver a iniciar sesión
              </Link>
            </p>
          </form>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>

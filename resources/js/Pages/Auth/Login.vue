<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
  canResetPassword: { type: Boolean },
  status: { type: String },
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), { onFinish: () => form.reset('password') });
};
</script>

<template>
  <GuestLayout>
    <Head title="Iniciar sesión" />

    <div class="min-h-[70vh] flex items-center justify-center p-6">
      <div class="w-full max-w-md">
        <div class="rounded-2xl shadow-lg p-8 border
                    bg-white/90 border-gray-100 text-gray-900
                    dark:bg-gray-900/80 dark:border-gray-800 dark:text-gray-100">
          <h1 class="text-2xl font-semibold mb-6">Entrar en FP Empresa</h1>

          <div v-if="status" class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
            {{ status }}
          </div>

          <form @submit.prevent="submit" class="space-y-5">
            <div>
              <InputLabel for="email" value="Correo electrónico" />
              <TextInput id="email" type="email" class="mt-1" v-model="form.email"
                         required autofocus autocomplete="username"
                         placeholder="tucorreo@ejemplo.com" />
              <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
              <InputLabel for="password" value="Contraseña" />
              <TextInput id="password" type="password" class="mt-1" v-model="form.password"
                         required autocomplete="current-password"
                         placeholder="••••••••" />
              <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
              <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <Checkbox name="remember" v-model:checked="form.remember" />
                <span>Recuérdame</span>
              </label>

              <Link v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-primary-600 hover:underline dark:text-primary-400">
                ¿Has olvidado tu contraseña?
              </Link>
            </div>

            <PrimaryButton class="w-full justify-center"
                           :class="{ 'opacity-25': form.processing }"
                           :disabled="form.processing">
              Entrar
            </PrimaryButton>

            <p class="text-sm text-gray-600 text-center dark:text-gray-300">
              ¿No tienes cuenta?
              <Link :href="route('register')" class="text-primary-600 hover:underline dark:text-primary-400">
                Regístrate
              </Link>
            </p>
          </form>
        </div>
      </div>
    </div>
  </GuestLayout>
</template>


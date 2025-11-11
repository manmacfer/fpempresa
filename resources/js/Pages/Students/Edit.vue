<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
  student: { type: Object, required: true },
})

const form = useForm({
  name: props.student.name ?? '',
})

const submit = () => {
  form.patch(route('students.update'))
}
</script>

<template>
  <Head title="Mi perfil" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
        Mi perfil
      </h2>
    </template>

    <div class="py-8">
      <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border bg-white/90 p-6 shadow-sm
                    dark:border-gray-800 dark:bg-gray-900/80 dark:text-gray-100">
          <form @submit.prevent="submit" class="space-y-6">
            <div>
              <InputLabel for="name" value="Nombre" />
              <TextInput id="name" v-model="form.name" class="mt-1 block w-full" />
              <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
              <InputLabel value="Correo (solo lectura)" />
              <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                {{ props.student.email }}
              </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Guardar cambios
              </PrimaryButton>

              <Link
                :href="route('students.public.show', props.student.id)"
                class="text-sm text-indigo-600 hover:underline dark:text-indigo-400"
              >
                Ver mi ficha pública
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

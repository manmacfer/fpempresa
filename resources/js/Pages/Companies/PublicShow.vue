<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  company: {
    type: Object,
    required: true,
    // Esperado: id, trade_name, legal_name, sector, website, linkedin, city, province, description, contact_name, contact_email
  },
})

const page = usePage()
const auth = computed(() => page.props.auth || {})
const isOwner = computed(() => {
  // si el usuario logueado es la misma empresa
  return auth.value?.companyId && props.company?.id && auth.value.companyId === props.company.id
})
const nameToShow = computed(() => props.company.trade_name || props.company.legal_name || 'Empresa')
</script>

<template>
  <AuthenticatedLayout>
    <div class="bg-gradient-to-b from-indigo-600/10 to-transparent dark:from-indigo-400/10">
      <div class="max-w-5xl mx-auto p-6">
        <!-- Cabecera -->
        <div class="rounded-2xl bg-white dark:bg-gray-900 shadow p-6 md:p-8">
          <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4 flex-1">
              <img
                v-if="props.company.logo_url"
                :src="props.company.logo_url"
                alt="Logo"
                class="h-20 w-20 rounded-lg object-cover ring-1 ring-gray-200 dark:ring-gray-700"
              />
              <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                  {{ nameToShow }}
                </h1>
                <div class="mt-2 flex flex-wrap gap-2 text-sm">
                  <span v-if="props.company.sector"
                        class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-indigo-700
                               dark:bg-indigo-400/20 dark:text-indigo-200">
                    {{ props.company.sector }}
                  </span>
                  <span v-if="props.company.city || props.company.province"
                        class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-gray-700
                               dark:bg-gray-800 dark:text-gray-300">
                    {{ props.company.city }}<span v-if="props.company.city && props.company.province"> — </span>{{ props.company.province }}
                  </span>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <Link v-if="isOwner" href="/companies/me/edit"
                    class="px-3 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50
                           dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">
                Editar mi perfil
              </Link>
            </div>
          </div>

          <!-- Enlaces -->
          <div class="mt-4 flex flex-wrap gap-3">
            <a v-if="props.company.website" :href="props.company.website" target="_blank" rel="noopener"
               class="inline-flex items-center rounded-xl bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-700">
              Web
            </a>
            <a v-if="props.company.linkedin" :href="props.company.linkedin" target="_blank" rel="noopener"
               class="inline-flex items-center rounded-xl bg-gray-900 px-3 py-1.5 text-white hover:bg-black
                      dark:bg-gray-700 dark:hover:bg-gray-600">
              LinkedIn
            </a>
          </div>
        </div>

        <!-- Cuerpo -->
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Sobre la empresa -->
          <section class="lg:col-span-2 rounded-2xl bg-white dark:bg-gray-900 shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Sobre la empresa</h2>
            <p v-if="props.company.description" class="mt-3 text-gray-700 dark:text-gray-300 whitespace-pre-line">
              {{ props.company.description }}
            </p>
            <p v-else class="mt-3 text-gray-500 dark:text-gray-400 italic">
              Esta empresa aún no ha añadido una descripción.
            </p>
          </section>

          <!-- Info breve -->
          <aside class="rounded-2xl bg-white dark:bg-gray-900 shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Información</h3>
            <dl class="space-y-3">
              <div v-if="props.company.legal_name" class="pb-3 border-b border-gray-200 dark:border-gray-700">
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
                  Nombre legal
                </dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ props.company.legal_name }}
                </dd>
              </div>
              
              <div v-if="props.company.trade_name" class="pb-3 border-b border-gray-200 dark:border-gray-700">
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
                  Nombre comercial
                </dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ props.company.trade_name }}
                </dd>
              </div>
              
              <div v-if="props.company.sector" class="pb-3 border-b border-gray-200 dark:border-gray-700">
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
                  Sector
                </dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ props.company.sector }}
                </dd>
              </div>
              
              <div v-if="props.company.city || props.company.province" class="pb-3 border-b border-gray-200 dark:border-gray-700">
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
                  Ubicación
                </dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ props.company.city }}<span v-if="props.company.city && props.company.province"> — </span>{{ props.company.province }}
                </dd>
              </div>
              
              <div v-if="props.company.contact_name" class="pb-3 border-b border-gray-200 dark:border-gray-700">
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
                  Persona de contacto
                </dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                  {{ props.company.contact_name }}
                </dd>
              </div>
              
              <div v-if="props.company.website" class="pb-3 border-b border-gray-200 dark:border-gray-700">
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
                  Web
                </dt>
                <dd class="text-sm">
                  <a :href="props.company.website" target="_blank" 
                     class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium inline-flex items-center gap-1">
                    {{ props.company.website }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                  </a>
                </dd>
              </div>
              
              <div v-if="props.company.linkedin" class="pb-3">
                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">
                  LinkedIn
                </dt>
                <dd class="text-sm">
                  <a :href="props.company.linkedin" target="_blank" 
                     class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium inline-flex items-center gap-1">
                    Ver perfil
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                  </a>
                </dd>
              </div>
            </dl>
          </aside>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
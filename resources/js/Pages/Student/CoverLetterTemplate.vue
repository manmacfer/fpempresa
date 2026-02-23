<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
</script>

<template>
  <Head title="Plantilla de Carta de Presentación" />

  <AuthenticatedLayout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 dark:from-gray-900 dark:to-gray-950">
      <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <!-- Header con gradiente -->
        <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 p-8 shadow-xl">
          <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
              <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <div>
              <h1 class="text-3xl font-bold text-white">Plantilla de Carta de Presentación</h1>
              <p class="mt-1 text-indigo-100">Guía para crear una carta profesional y efectiva</p>
            </div>
          </div>
        </div>

        <!-- Introducción -->
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
          <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/30">
              <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div>
              <h2 class="text-xl font-semibold text-gray-900 dark:text-white">¿Para qué sirve?</h2>
              <p class="mt-2 text-gray-600 dark:text-gray-300">
                A continuación se presenta una plantilla con recomendaciones para redactar tu carta de presentación. 
                Esta carta debe adjuntarse junto al CV y servirá para destacar tu motivación, tus habilidades y por qué eres una buena opción para las prácticas en empresa.
              </p>
            </div>
          </div>
        </div>

        <!-- Sección 1: Encabezado -->
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
          <div class="mb-6 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500 text-white font-bold text-lg shadow-md">
              1
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Encabezado</h2>
          </div>
          
          <div class="space-y-3 pl-13">
            <div class="flex items-start gap-3">
              <svg class="mt-1 h-5 w-5 flex-shrink-0 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Nombre y apellidos</span></p>
            </div>
            <div class="flex items-start gap-3">
              <svg class="mt-1 h-5 w-5 flex-shrink-0 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Dirección completa</span></p>
            </div>
            <div class="flex items-start gap-3">
              <svg class="mt-1 h-5 w-5 flex-shrink-0 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Teléfono</span> | <span class="font-semibold">Correo electrónico</span></p>
            </div>
            <div class="flex items-start gap-3">
              <svg class="mt-1 h-5 w-5 flex-shrink-0 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
              <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Fecha y lugar</span></p>
            </div>
          </div>
        </div>

        <!-- Sección 2: Cuerpo de la Carta -->
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
          <div class="mb-6 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500 text-white font-bold text-lg shadow-md">
              2
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cuerpo de la Carta</h2>
          </div>

          <div class="space-y-6 pl-13">
            <!-- Saludo inicial -->
            <div class="rounded-xl bg-gradient-to-r from-indigo-50 to-purple-50 p-6 dark:from-indigo-950/30 dark:to-purple-950/30">
              <div class="mb-3 flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-indigo-600"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Saludo inicial</h3>
              </div>
              <p class="text-gray-700 dark:text-gray-300">
                Dirige la carta a la empresa o al responsable de selección si conoces su nombre. Si no, puedes usar: 
                <span class="font-medium italic">"A la atención del departamento de RRHH"</span> o 
                <span class="font-medium italic">"Estimado/a responsable de selección"</span>.
              </p>
            </div>

            <!-- Primer párrafo -->
            <div class="rounded-xl bg-gradient-to-r from-blue-50 to-cyan-50 p-6 dark:from-blue-950/30 dark:to-cyan-950/30">
              <div class="mb-3 flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-blue-600"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Primer párrafo</h3>
              </div>
              <p class="text-gray-700 dark:text-gray-300">
                Preséntate brevemente (nombre, ciclo formativo, centro educativo). 
                Indica el motivo de la carta: realizar prácticas en la empresa.
              </p>
            </div>

            <!-- Segundo párrafo -->
            <div class="rounded-xl bg-gradient-to-r from-purple-50 to-pink-50 p-6 dark:from-purple-950/30 dark:to-pink-950/30">
              <div class="mb-3 flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-purple-600"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Segundo párrafo</h3>
              </div>
              <p class="text-gray-700 dark:text-gray-300">
                Destaca tus competencias, motivación, experiencia previa (si la tienes), 
                intereses o habilidades personales que te hacen idóneo/a para el puesto.
              </p>
            </div>

            <!-- Tercer párrafo -->
            <div class="rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 p-6 dark:from-green-950/30 dark:to-emerald-950/30">
              <div class="mb-3 flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-green-600"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tercer párrafo</h3>
              </div>
              <p class="text-gray-700 dark:text-gray-300">
                Explica por qué te interesa esa empresa o sector. 
                Muestra interés y predisposición para aprender y adaptarte.
              </p>
            </div>

            <!-- Despedida -->
            <div class="rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 p-6 dark:from-amber-950/30 dark:to-orange-950/30">
              <div class="mb-3 flex items-center gap-2">
                <div class="h-2 w-2 rounded-full bg-amber-600"></div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Despedida</h3>
              </div>
              <p class="text-gray-700 dark:text-gray-300">
                Agradece la atención prestada y ofrece tu disponibilidad para ampliar información o realizar una entrevista. 
                Finaliza con una despedida cordial.
              </p>
            </div>
          </div>
        </div>

        <!-- Sección 3: Recomendaciones Finales -->
        <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
          <div class="mb-6 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-500 text-white font-bold text-lg shadow-md">
              3
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Recomendaciones Finales</h2>
          </div>

          <div class="space-y-4 pl-13">
            <div class="flex items-start gap-3">
              <div class="mt-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
              </div>
              <p class="text-gray-700 dark:text-gray-300">
                Usa un <span class="font-semibold text-indigo-600 dark:text-indigo-400">lenguaje claro, correcto y profesional</span>.
              </p>
            </div>

            <div class="flex items-start gap-3">
              <div class="mt-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
              </div>
              <p class="text-gray-700 dark:text-gray-300">
                <span class="font-semibold text-indigo-600 dark:text-indigo-400">Evita errores ortográficos</span>.
              </p>
            </div>

            <div class="flex items-start gap-3">
              <div class="mt-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
              </div>
              <p class="text-gray-700 dark:text-gray-300">
                <span class="font-semibold text-indigo-600 dark:text-indigo-400">No repitas el contenido del CV</span>. Amplía información o añade aspectos personales.
              </p>
            </div>

            <div class="flex items-start gap-3">
              <div class="mt-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
              </div>
              <p class="text-gray-700 dark:text-gray-300">
                La carta debe ser <span class="font-semibold text-indigo-600 dark:text-indigo-400">breve: entre media y una página</span>.
              </p>
            </div>

            <div class="flex items-start gap-3">
              <div class="mt-1 flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
              </div>
              <p class="text-gray-700 dark:text-gray-300">
                <span class="font-semibold text-indigo-600 dark:text-indigo-400">Personaliza la carta</span> para cada empresa o sector si es posible.
              </p>
            </div>
          </div>
        </div>

        <!-- Footer con consejo -->
        <div class="rounded-2xl bg-gradient-to-r from-green-100 to-emerald-100 p-6 shadow-lg dark:from-green-900/30 dark:to-emerald-900/30">
          <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-white/50 backdrop-blur-sm dark:bg-gray-800/50">
              <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
              </svg>
            </div>
            <div>
              <h3 class="mb-2 text-lg font-bold text-green-800 dark:text-green-300">💡 Consejo final</h3>
              <p class="text-green-700 dark:text-green-400">
                Recuerda que la carta de presentación es tu primera impresión. 
                Dedica tiempo a redactarla bien, revísala varias veces y, si es posible, 
                pide a alguien que la lea antes de enviarla. ¡Mucho éxito en tu búsqueda de prácticas!
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

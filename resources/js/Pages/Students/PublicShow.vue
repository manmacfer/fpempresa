<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  student: { type: Object, required: true },
})
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Perfil de {{ student.name || 'Alumno' }}</h2>
    </template>

    <div class="mx-auto max-w-5xl p-6 space-y-8">
      <section class="flex items-center gap-4">
        <img v-if="student.avatar_url" :src="student.avatar_url" alt="avatar" class="h-20 w-20 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700" />
        <div>
          <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ student.name }}</div>
          <div class="text-gray-600 dark:text-gray-300">{{ student.email }}</div>
        </div>
      </section>

      <section class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Resumen</h3>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 text-sm text-gray-700 dark:text-gray-300">
          <div><strong>Ciudad:</strong> {{ student.city || '—' }}</div>
          <div><strong>Ciclo:</strong> {{ student.cycle || '—' }}</div>
          <div><strong>Centro:</strong> {{ student.center || '—' }}</div>
          <div><strong>Modalidad FP:</strong> {{ student.fp_modality || '—' }}</div>
          <div><strong>Disponibilidad:</strong> {{ student.availability_slot || '—' }}</div>
          <div><strong>Modalidad trabajo:</strong> {{ student.work_modality || '—' }}</div>
        </div>
      </section>

      <section v-if="(student.tech_competencies?.length || student.soft_skills?.length)" class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Competencias</h3>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div v-if="student.tech_competencies?.length">
            <h4 class="mb-1 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Técnicas</h4>
            <div class="flex flex-wrap gap-2">
              <span v-for="(sk,i) in student.tech_competencies" :key="i" class="rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800">{{ sk }}</span>
            </div>
          </div>
          <div v-if="student.soft_skills?.length">
            <h4 class="mb-1 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Soft skills</h4>
            <div class="flex flex-wrap gap-2">
              <span v-for="(ss,i) in student.soft_skills" :key="i" class="rounded-full bg-gray-100 px-3 py-1 text-xs dark:bg-gray-800">{{ ss }}</span>
            </div>
          </div>
        </div>
      </section>

      <section v-if="student.languages?.length" class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Idiomas</h3>
        <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
          <li v-for="(lng,i) in student.languages" :key="i">
            <strong>{{ lng.name }}</strong><span v-if="lng.level"> · {{ lng.level }}</span>
          </li>
        </ul>
      </section>

      <section v-if="student.educations?.length" class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Formación</h3>
        <ul class="space-y-2 text-sm">
          <li v-for="e in student.educations" :key="e.id" class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/60">
            <div class="font-medium text-gray-800 dark:text-gray-100">{{ e.title }}</div>
            <div class="text-gray-500 dark:text-gray-400">
              <span v-if="e.center">{{ e.center }} · </span>
              <span>{{ e.start_date || '—' }} — {{ e.end_date || 'Actual' }}</span>
            </div>
          </li>
        </ul>
      </section>

      <section v-if="student.experiences?.length" class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Experiencia</h3>
        <ul class="space-y-2 text-sm">
          <li v-for="x in student.experiences" :key="x.id" class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800/60">
            <div class="font-medium text-gray-800 dark:text-gray-100">{{ x.company }} <span v-if="x.position" class="text-gray-500 dark:text-gray-400">· {{ x.position }}</span></div>
            <div class="text-gray-500 dark:text-gray-400">
              <span>{{ x.start_date || '—' }} — {{ x.end_date || 'Actual' }}</span>
              <span v-if="x.is_non_formal" class="ms-2 rounded bg-gray-200 px-2 py-0.5 text-[11px] dark:bg-gray-700">No formal</span>
            </div>
            <div v-if="x.functions" class="mt-1 text-gray-700 dark:text-gray-300">{{ x.functions }}</div>
          </li>
        </ul>
      </section>

      <section v-if="(student.link_linkedin || student.link_portfolio || student.link_github || student.link_video)" class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Enlaces</h3>
        <ul class="space-y-1 text-sm">
          <li v-if="student.link_linkedin"><a :href="student.link_linkedin" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">LinkedIn</a></li>
          <li v-if="student.link_portfolio"><a :href="student.link_portfolio" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">Portfolio</a></li>
          <li v-if="student.link_github"><a :href="student.link_github" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">GitHub</a></li>
          <li v-if="student.link_video"><a :href="student.link_video" target="_blank" class="text-indigo-600 hover:underline dark:text-indigo-400">Vídeo</a></li>
        </ul>
      </section>
    </div>
  </AuthenticatedLayout>
</template>

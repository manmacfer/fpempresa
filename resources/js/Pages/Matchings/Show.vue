<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    matching: {
        type: Object,
        required: true
    }
});

const getStatusBadge = () => {
    if (props.matching.validado_centro) {
        return { text: 'Validado', class: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' };
    }
    if (props.matching.student_matched && props.matching.company_matched) {
        return { text: 'Pendiente Validación', class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200' };
    }
    return { text: 'Incompleto', class: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' };
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="Detalles del Matching" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                            Detalles del Matching #{{ matching.id }}
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Creado el {{ formatDate(matching.created_at) }}
                        </p>
                    </div>
                    <Link
                        :href="route('admin.matchings.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors"
                    >
                        ← Volver
                    </Link>
                </div>

                <!-- Estado -->
                <div class="mb-6">
                    <span :class="['inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold', getStatusBadge().class]">
                        {{ getStatusBadge().text }}
                    </span>
                </div>

                <!-- Grid de información -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Estudiante -->
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            👨‍🎓 Estudiante
                        </h2>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Nombre</p>
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ matching.student?.first_name }} {{ matching.student?.last_name }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ matching.student?.user?.email }}</p>
                            </div>
                            <div v-if="matching.student?.cycle">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Ciclo</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ matching.student.cycle }}</p>
                            </div>
                            <div v-if="matching.student?.city">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Ubicación</p>
                                <p class="text-gray-900 dark:text-gray-100">
                                    {{ matching.student.city }}, {{ matching.student.province }}
                                </p>
                            </div>
                            <div v-if="matching.student?.phone">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Teléfono</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ matching.student.phone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Empresa -->
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            🏢 Empresa
                        </h2>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Nombre</p>
                                <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    {{ matching.company?.trade_name || matching.company?.legal_name }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ matching.company?.user?.email }}</p>
                            </div>
                            <div v-if="matching.company?.city">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Ubicación</p>
                                <p class="text-gray-900 dark:text-gray-100">
                                    {{ matching.company.city }}, {{ matching.company.province }}
                                </p>
                            </div>
                            <div v-if="matching.company?.phone">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Teléfono</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ matching.company.phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vacante -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        💼 Vacante
                    </h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Título</p>
                            <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ matching.vacancy?.title }}
                            </p>
                        </div>
                        <div v-if="matching.vacancy?.description">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Descripción</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ matching.vacancy.description }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div v-if="matching.vacancy?.mode">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Modalidad</p>
                                <p class="text-gray-900 dark:text-gray-100 capitalize">{{ matching.vacancy.mode }}</p>
                            </div>
                            <div v-if="matching.vacancy?.city">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Ubicación</p>
                                <p class="text-gray-900 dark:text-gray-100">
                                    {{ matching.vacancy.city }}, {{ matching.vacancy.province }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estado del Matching -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Estado del Matching
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-gray-700 dark:text-gray-300">Estudiante aceptó</span>
                            <span v-if="matching.student_matched" class="text-green-600 dark:text-green-400 font-semibold">✓ Sí</span>
                            <span v-else class="text-gray-400 dark:text-gray-500">✗ No</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-gray-700 dark:text-gray-300">Empresa aceptó</span>
                            <span v-if="matching.company_matched" class="text-green-600 dark:text-green-400 font-semibold">✓ Sí</span>
                            <span v-else class="text-gray-400 dark:text-gray-500">✗ No</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <span class="text-gray-700 dark:text-gray-300">Validado por centro</span>
                            <span v-if="matching.validado_centro" class="text-green-600 dark:text-green-400 font-semibold">✓ Sí</span>
                            <span v-else class="text-gray-400 dark:text-gray-500">✗ No</span>
                        </div>
                    </div>

                    <div v-if="matching.comentarios_centro" class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Comentarios del centro</p>
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <p class="text-gray-900 dark:text-gray-100">{{ matching.comentarios_centro }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

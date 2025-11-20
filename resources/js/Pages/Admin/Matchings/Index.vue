<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    matchings: {
        type: Array,
        default: () => []
    },
    status: {
        type: String,
        default: 'all'
    }
});

const activeTab = ref(props.status || 'all');

const tabs = [
    { value: 'all', label: 'Todos', color: 'gray' },
    { value: 'pending', label: 'Pendientes Validación', color: 'yellow' },
    { value: 'validated', label: 'Validados', color: 'green' },
    { value: 'incomplete', label: 'Incompletos', color: 'orange' },
    { value: 'complete', label: 'Completos', color: 'blue' }
];

const changeTab = (status) => {
    activeTab.value = status;
    router.get(route('admin.matchings.index'), { status }, {
        preserveState: true,
        preserveScroll: true
    });
};

const getStatusBadgeClass = (matching) => {
    if (matching.estado === 'completed') return 'bg-blue-100 text-blue-800';
    if (matching.validado_centro) return 'bg-green-100 text-green-800';
    if (!matching.validado_centro && matching.estado === 'pending') return 'bg-yellow-100 text-yellow-800';
    return 'bg-orange-100 text-orange-800';
};

const getStatusText = (matching) => {
    if (matching.estado === 'completed') return 'Completo';
    if (matching.validado_centro) return 'Validado';
    if (!matching.validado_centro && matching.estado === 'pending') return 'Pendiente Validación';
    return 'Incompleto';
};

const validationForm = useForm({
    validado_centro: false,
    comentarios_centro: ''
});

const toggleValidation = (matching) => {
    validationForm.validado_centro = !matching.validado_centro;
    validationForm.comentarios_centro = matching.comentarios_centro || '';
    
    validationForm.patch(route('admin.matchings.validation', matching.id), {
        preserveScroll: true,
        onSuccess: () => {
            validationForm.reset();
        }
    });
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('es-ES');
};
</script>

<template>
    <Head title="Gestión de Matchings" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">Gestión de Matchings</h2>
                            <Link :href="route('admin.dashboard')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                ← Volver al Panel
                            </Link>
                        </div>

                        <!-- Tabs -->
                        <div class="flex space-x-2 mb-6 border-b border-gray-200 dark:border-gray-700">
                            <button
                                v-for="tab in tabs"
                                :key="tab.value"
                                @click="changeTab(tab.value)"
                                :class="[
                                    'px-4 py-2 font-medium text-sm transition-colors border-b-2',
                                    activeTab === tab.value
                                        ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400'
                                        : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'
                                ]"
                            >
                                {{ tab.label }}
                            </button>
                        </div>

                        <!-- Matchings Table -->
                        <div v-if="matchings.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Estudiante
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Empresa
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Vacante
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Fecha
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="matching in matchings" :key="matching.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            #{{ matching.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ matching.student?.first_name }} {{ matching.student?.last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ matching.student?.user?.email }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ matching.company?.company_name || matching.company?.name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ matching.vacancy?.title }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ matching.vacancy?.city }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                getStatusBadgeClass(matching)
                                            ]">
                                                {{ getStatusText(matching) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDate(matching.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex space-x-2">
                                                <!-- Botón Validar/Invalidar - Solo si NO está completo -->
                                                <button
                                                    v-if="matching.estado !== 'completed'"
                                                    @click="toggleValidation(matching)"
                                                    :disabled="validationForm.processing"
                                                    :class="[
                                                        'px-3 py-1.5 rounded-lg text-white text-xs font-semibold transition-all shadow-sm',
                                                        matching.validado_centro
                                                            ? 'bg-red-600 hover:bg-red-700 hover:shadow-md'
                                                            : 'bg-green-600 hover:bg-green-700 hover:shadow-md',
                                                        validationForm.processing ? 'opacity-50 cursor-not-allowed' : ''
                                                    ]"
                                                >
                                                    {{ matching.validado_centro ? '✗ Invalidar' : '✓ Validar' }}
                                                </button>
                                                
                                                <!-- Botón Ver Seguimiento - Solo si está completo -->
                                                <Link
                                                    v-if="matching.estado === 'completed'"
                                                    :href="route('teacher.seguimiento.show', matching.id)"
                                                    class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-all shadow-sm hover:shadow-md inline-flex items-center"
                                                >
                                                    📊 Seguimiento
                                                </Link>
                                                
                                                <!-- Botón Ver Detalles -->
                                                <Link
                                                    :href="route('matchings.show', matching.id)"
                                                    class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold transition-all shadow-sm hover:shadow-md inline-flex items-center"
                                                >
                                                    👁 Detalles
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay matchings</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                No se encontraron matchings con los filtros seleccionados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

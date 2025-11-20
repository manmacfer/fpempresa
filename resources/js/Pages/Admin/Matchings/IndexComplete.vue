<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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
    { value: 'all', label: 'Todos' },
    { value: 'pending', label: 'Pendientes Validación' },
    { value: 'validated', label: 'Validados' },
    { value: 'incomplete', label: 'Incompletos' },
    { value: 'complete', label: 'Completos' }
];

const changeTab = (status) => {
    activeTab.value = status;
    router.get(route('admin.matchings.index'), { status }, {
        preserveState: true,
        preserveScroll: true
    });
};

const getStatusBadgeClass = (matching) => {
    if (matching.student_matched && matching.company_matched) {
        return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
    }
    if (matching.validado_centro) {
        return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    }
    if (!matching.validado_centro) {
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
    }
    return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
};

const getStatusText = (matching) => {
    if (matching.student_matched && matching.company_matched) {
        return 'Completo';
    }
    if (matching.validado_centro) {
        return 'Validado';
    }
    if (!matching.validado_centro) {
        return 'Pendiente Validación';
    }
    return 'Incompleto';
};

const toggleValidation = (matching) => {
    router.patch(
        route('admin.matchings.validation', matching.id),
        {
            validado_centro: !matching.validado_centro,
            comentarios_centro: matching.comentarios_centro || ''
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                // Recargar la página para reflejar cambios
            }
        }
    );
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
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                                Gestión de Matchings
                            </h2>
                            <Link 
                                :href="route('admin.dashboard')" 
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                ← Volver al Panel
                            </Link>
                        </div>

                        <!-- Tabs -->
                        <div class="flex space-x-2 mb-6 border-b dark:border-gray-700">
                            <button
                                v-for="tab in tabs"
                                :key="tab.value"
                                @click="changeTab(tab.value)"
                                :class="[
                                    'px-4 py-2 font-medium text-sm transition-colors border-b-2',
                                    activeTab === tab.value
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-200'
                                ]"
                            >
                                {{ tab.label }}
                            </button>
                        </div>

                        <!-- Matchings Table -->
                        <div v-if="matchings.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
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
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="matching in matchings" :key="matching.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
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
                                                {{ matching.company?.trade_name || matching.company?.name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ matching.company?.user?.email }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ matching.vacancy?.title || 'Sin vacante' }}
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <button
                                                @click="toggleValidation(matching)"
                                                :class="[
                                                    'px-3 py-1 rounded-md text-white text-xs font-medium transition-colors',
                                                    matching.validado_centro
                                                        ? 'bg-red-600 hover:bg-red-700'
                                                        : 'bg-green-600 hover:bg-green-700'
                                                ]"
                                            >
                                                {{ matching.validado_centro ? 'Invalidar' : 'Validar' }}
                                            </button>
                                            <Link
                                                v-if="matching.conversation"
                                                :href="route('seguimiento.show', matching.id)"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                            >
                                                Ver Seguimiento
                                            </Link>
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay matchings</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                No se encontraron matchings con los filtros seleccionados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

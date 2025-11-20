<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    vacancies: {
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
    { value: 'all', label: 'Todas' },
    { value: 'published', label: 'Publicadas' },
    { value: 'closed', label: 'Cerradas' }
];

const changeTab = (status) => {
    activeTab.value = status;
    router.get(route('admin.vacancies.index'), { status }, {
        preserveState: true,
        preserveScroll: true
    });
};

const getStatusBadgeClass = (vacancy) => {
    if (vacancy.status === 'published') {
        return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
    }
    if (vacancy.status === 'closed') {
        return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
    }
    return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
};

const getStatusText = (vacancy) => {
    if (vacancy.status === 'published') return 'Publicada';
    if (vacancy.status === 'closed') return 'Cerrada';
    return 'Borrador';
};

const changeStatus = (vacancy, newStatus) => {
    router.patch(
        route('admin.vacancies.status', vacancy.id),
        { status: newStatus },
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
    <Head title="Gestión de Vacantes" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                                Gestión de Vacantes
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

                        <!-- Vacancies Table -->
                        <div v-if="vacancies.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Título
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Empresa
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Ubicación
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Candidaturas
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Publicada
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="vacancy in vacancies" :key="vacancy.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ vacancy.title }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ vacancy.cycle_required }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ vacancy.company?.trade_name || vacancy.company?.name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ vacancy.company?.sector }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ vacancy.city }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ vacancy.province }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="[
                                                'px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full',
                                                getStatusBadgeClass(vacancy)
                                            ]">
                                                {{ getStatusText(vacancy) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ vacancy.applications_count || 0 }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDate(vacancy.published_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <button
                                                v-if="vacancy.status !== 'published'"
                                                @click="changeStatus(vacancy, 'published')"
                                                class="px-3 py-1 rounded-md bg-green-600 hover:bg-green-700 text-white text-xs font-medium transition-colors"
                                            >
                                                Publicar
                                            </button>
                                            <button
                                                v-if="vacancy.status === 'published'"
                                                @click="changeStatus(vacancy, 'closed')"
                                                class="px-3 py-1 rounded-md bg-red-600 hover:bg-red-700 text-white text-xs font-medium transition-colors"
                                            >
                                                Cerrar
                                            </button>
                                            <Link
                                                :href="route('vacancies.show', vacancy.id)"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                            >
                                                Ver Detalles
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay vacantes</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                No se encontraron vacantes con los filtros seleccionados.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

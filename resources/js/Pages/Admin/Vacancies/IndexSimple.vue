<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    vacancies: Array,
    status: String
});
</script>

<template>
    <Head title="Vacancies" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h1 class="text-2xl font-bold mb-4">Gestión de Vacantes</h1>
                    <p>TEST: Esta es la página de vacantes</p>
                    <p>Status: {{ status }}</p>
                    <p>Número de vacantes: {{ vacancies ? vacancies.length : 0 }}</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

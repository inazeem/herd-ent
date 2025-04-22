<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    encounters: Object,
    filters: Object,
    statuses: Object,
});
</script>

<template>
    <Head title="Encounters" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Encounters</h2>
                <Link
                    v-if="$page.props.can.create_encounters"
                    :href="route('encounters.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    New Encounter
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="encounters.data.length > 0">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Patient
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Clinician
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Chief Complaint
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="encounter in encounters.data" :key="encounter.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ new Date(encounter.created_at).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ encounter.patient?.first_name }} {{ encounter.patient?.last_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            Dr. {{ encounter.clinician?.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ encounter.chief_complaint }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                :class="{
                                                    'px-2 py-1 rounded text-xs font-medium': true,
                                                    'bg-blue-100 text-blue-800': encounter.status === 'in-progress',
                                                    'bg-green-100 text-green-800': encounter.status === 'completed',
                                                    'bg-amber-100 text-amber-800': encounter.status === 'billed'
                                                }"
                                            >
                                                {{ statuses[encounter.status] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('encounters.show', encounter.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-3">View</Link>
                                            <Link v-if="$page.props.can.edit_encounters" :href="route('encounters.edit', encounter.id)" class="text-amber-600 dark:text-amber-400 hover:text-amber-900 mr-3">Edit</Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div class="mt-6">
                                <!-- Pagination component would go here -->
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <p>No encounters found.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
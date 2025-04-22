<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    patients: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

// Handle search input with debounce to prevent too many requests
watch(search, debounce(function (value) {
    router.get(route('patients.index'), {search: value}, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Add a method to handle patient deletion with confirmation
const deletePatient = (patientId) => {
    if (window.confirm('Are you sure you want to delete this patient?')) {
        router.delete(route('patients.destroy', patientId));
    }
};
</script>

<template>
    <Head title="Patients" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-wrap justify-between items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Patients</h2>
                <Link
                    v-if="$page.props.can.create_patients"
                    :href="route('patients.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    Add Patient
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Search and Filter Controls -->
                <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Search -->
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                                <input
                                    id="search"
                                    v-model="search"
                                    type="text"
                                    placeholder="Search by name, ID, or phone..."
                                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patients Table/Cards -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="patients.data.length > 0">
                            <!-- Desktop view - Table (hidden on small screens) -->
                            <div class="hidden md:block overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                ID
                                            </th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Phone
                                            </th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                DOB
                                            </th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="patient in patients.data" :key="patient.id">
                                            <td class="px-6 py-4 whitespace-nowrap">{{ patient.patient_id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ patient.first_name }} {{ patient.last_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ patient.phone }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ patient.date_of_birth }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <Link :href="route('patients.show', patient.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-3">View</Link>
                                                <Link v-if="$page.props.can.edit_patients" :href="route('patients.edit', patient.id)" class="text-amber-600 dark:text-amber-400 hover:text-amber-900 mr-3">Edit</Link>
                                                <button
                                                    v-if="$page.props.can.delete_patients"
                                                    @click="deletePatient(patient.id)"
                                                    class="text-red-600 dark:text-red-400 hover:text-red-900"
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Mobile view - Cards (hidden on medium and larger screens) -->
                            <div class="block md:hidden">
                                <div v-for="patient in patients.data" :key="patient.id" 
                                     class="mb-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="font-semibold text-lg">{{ patient.first_name }} {{ patient.last_name }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ patient.patient_id }}</p>
                                        </div>
                                        <div class="flex">
                                            <Link :href="route('patients.show', patient.id)" 
                                                class="p-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                            <Link v-if="$page.props.can.edit_patients" :href="route('patients.edit', patient.id)" 
                                                class="p-2 text-amber-600 dark:text-amber-400 hover:text-amber-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button v-if="$page.props.can.delete_patients" @click="deletePatient(patient.id)"
                                                class="p-2 text-red-600 dark:text-red-400 hover:text-red-900">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <span class="font-medium">Phone:</span>
                                            <span class="block">{{ patient.phone }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium">DOB:</span>
                                            <span class="block">{{ patient.date_of_birth }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-6">
                                <Pagination :links="patients.links" />
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <p>No patients found. Please add your first patient.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
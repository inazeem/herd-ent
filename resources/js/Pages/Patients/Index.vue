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
            <div class="flex justify-between items-center">
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
                    <div class="p-6">
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

                <!-- Patients Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="patients.data.length > 0">
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
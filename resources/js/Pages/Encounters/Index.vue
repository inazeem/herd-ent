<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    encounters: Object,
    filters: Object,
    statuses: Object,
});

const search = ref(props.filters?.search || '');
const currentStatus = ref(props.filters?.status || '');

// Update search results with debounce
const updateSearch = debounce((value) => {
    router.get(route('encounters.index'), { 
        search: value, 
        status: currentStatus.value 
    }, {
        preserveState: true,
        replace: true
    });
}, 300);

// Watch for changes to the search input
watch(search, (value) => {
    updateSearch(value);
});

// Handle status filter change
const handleStatusChange = (event) => {
    const newStatus = event.target.value;
    currentStatus.value = newStatus;
    router.get(route('encounters.index'), {
        status: newStatus,
        search: search.value
    }, {
        preserveState: true,
        replace: false
    });
};
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
                <!-- Search and filter controls -->
                <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap gap-4 items-center">
                            <!-- Search input -->
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    v-model="search"
                                    placeholder="Search by patient name..." 
                                    class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                            </div>
                            
                            <!-- Status filter dropdown -->
                            <div class="flex items-center">
                                <label for="status" class="mr-2 text-sm font-medium text-gray-700 dark:text-gray-300">Status:</label>
                                <select 
                                    id="status" 
                                    v-model="currentStatus"
                                    @change="handleStatusChange"
                                    class="border border-gray-300 dark:border-gray-600 rounded-md p-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">All</option>
                                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
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
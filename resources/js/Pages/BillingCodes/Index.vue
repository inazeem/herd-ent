<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    billingCodes: Object,
    filters: Object,
    codeTypes: Object,
});
</script>

<template>
    <Head title="Billing Codes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Billing Codes</h2>
                <Link
                    v-if="$page.props.can.manage_billing_codes"
                    :href="route('billing-codes.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    Add Billing Code
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div v-if="billingCodes.data.length > 0">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Code
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Price
                                        </th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="code in billingCodes.data" :key="code.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span 
                                                :class="{
                                                    'px-2 py-1 rounded text-xs font-medium': true,
                                                    'bg-blue-100 text-blue-800': code.code_type === 'CPT',
                                                    'bg-green-100 text-green-800': code.code_type === 'ICD-10',
                                                    'bg-amber-100 text-amber-800': code.code_type === 'HCPCS'
                                                }"
                                            >
                                                {{ code.code_type }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-medium">{{ code.code }}</td>
                                        <td class="px-6 py-4">{{ code.description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">${{ code.price !== undefined && code.price !== null ? parseFloat(code.price).toFixed(2) : '0.00' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('billing-codes.show', code.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-3">View</Link>
                                            <Link v-if="$page.props.can.manage_billing_codes" :href="route('billing-codes.edit', code.id)" class="text-amber-600 dark:text-amber-400 hover:text-amber-900 mr-3">Edit</Link>
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
                            <p>No billing codes found.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
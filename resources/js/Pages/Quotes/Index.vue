<template>
    <Head title="Quotes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Quotes</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-between mb-6">
                    <Link
                        v-if="$page.props.can.create_quotes"
                        :href="route('quotes.create')"
                        class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600"
                    >
                        Create Quote
                    </Link>
                </div>

                <DocumentFilters
                    :filters="filters"
                    :statuses="statuses"
                    date-field="quote_date"
                    route-name="quotes.index"
                />

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="text-left font-bold">
                                        <th class="pb-4 pt-6 px-6">Quote Number</th>
                                        <th class="pb-4 pt-6 px-6">Client</th>
                                        <th class="pb-4 pt-6 px-6">Date</th>
                                        <th class="pb-4 pt-6 px-6">Valid Until</th>
                                        <th class="pb-4 pt-6 px-6">Amount</th>
                                        <th class="pb-4 pt-6 px-6">Status</th>
                                        <th class="pb-4 pt-6 px-6">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="quote in quotes.data" :key="quote.id" class="hover:bg-gray-100">
                                        <td class="border-t px-6 py-4">{{ quote.quote_number }}</td>
                                        <td class="border-t px-6 py-4">{{ quote.client.name }}</td>
                                        <td class="border-t px-6 py-4">{{ formatDate(quote.quote_date) }}</td>
                                        <td class="border-t px-6 py-4">{{ formatDate(quote.valid_until) }}</td>
                                        <td class="border-t px-6 py-4">{{ formatAmount(quote.total) }}</td>
                                        <td class="border-t px-6 py-4">
                                            <span :class="getStatusClass(quote.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                                                {{ statuses[quote.status] }}
                                            </span>
                                        </td>
                                        <td class="border-t px-6 py-4">
                                            <Link
                                                v-if="$page.props.can.edit_quotes"
                                                :href="route('quotes.edit', quote.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Edit
                                            </Link>
                                            <Link
                                                :href="route('quotes.show', quote.id)"
                                                class="text-blue-600 hover:text-blue-900 mr-3"
                                            >
                                                View
                                            </Link>
                                            <button
                                                v-if="$page.props.can.delete_quotes"
                                                @click="deleteQuote(quote)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="quotes.data.length === 0">
                                        <td colspan="7" class="border-t px-6 py-4 text-center text-gray-500">
                                            No quotes found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="quotes.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DocumentFilters from '@/Components/Filters/DocumentFilters.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    quotes: Object,
    filters: Object,
    statuses: Object,
});

function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

function formatAmount(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function getStatusClass(status) {
    const classes = {
        draft: 'bg-gray-100 text-gray-800',
        sent: 'bg-blue-100 text-blue-800',
        accepted: 'bg-green-100 text-green-800',
        declined: 'bg-red-100 text-red-800',
        expired: 'bg-yellow-100 text-yellow-800'
    };
    return classes[status] || classes.draft;
}

function deleteQuote(quote) {
    if (confirm(`Are you sure you want to delete quote ${quote.quote_number}?`)) {
        router.delete(route('quotes.destroy', quote.id));
    }
}
</script> 
<template>
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form @submit.prevent="applyFilters">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <InputLabel for="search" value="Search" />
                    <TextInput
                        id="search"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.search"
                        placeholder="Search by number or client..."
                    />
                </div>

                <!-- Status -->
                <div>
                    <InputLabel for="status" value="Status" />
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Statuses</option>
                        <option v-for="(label, value) in statuses" :key="value" :value="value">
                            {{ label }}
                        </option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <InputLabel for="date_from" value="Date From" />
                    <TextInput
                        id="date_from"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="form.date_from"
                    />
                </div>

                <div>
                    <InputLabel for="date_to" value="Date To" />
                    <TextInput
                        id="date_to"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="form.date_to"
                    />
                </div>

                <!-- Amount Range -->
                <div>
                    <InputLabel for="min_amount" value="Min Amount" />
                    <TextInput
                        id="min_amount"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        v-model="form.min_amount"
                    />
                </div>

                <div>
                    <InputLabel for="max_amount" value="Max Amount" />
                    <TextInput
                        id="max_amount"
                        type="number"
                        step="0.01"
                        class="mt-1 block w-full"
                        v-model="form.max_amount"
                    />
                </div>

                <!-- Sort -->
                <div>
                    <InputLabel for="sort_field" value="Sort By" />
                    <select
                        id="sort_field"
                        v-model="form.sort_field"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Select Field</option>
                        <option value="created_at">Date Created</option>
                        <option :value="dateField">Date</option>
                        <option value="total">Amount</option>
                        <option value="status">Status</option>
                    </select>
                </div>

                <div>
                    <InputLabel for="sort_direction" value="Sort Direction" />
                    <select
                        id="sort_direction"
                        v-model="form.sort_direction"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-end space-x-3">
                <SecondaryButton @click="resetFilters">
                    Reset
                </SecondaryButton>
                <PrimaryButton type="submit" :disabled="form.processing">
                    Apply Filters
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    filters: {
        type: Object,
        default: () => ({}),
    },
    statuses: {
        type: Object,
        required: true,
    },
    dateField: {
        type: String,
        required: true,
    },
    routeName: {
        type: String,
        required: true,
    },
});

const form = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    min_amount: props.filters.min_amount || '',
    max_amount: props.filters.max_amount || '',
    sort_field: props.filters.sort_field || '',
    sort_direction: props.filters.sort_direction || 'desc',
});

function applyFilters() {
    form.get(route(props.routeName), {
        preserveState: true,
        preserveScroll: true,
    });
}

function resetFilters() {
    form.search = '';
    form.status = '';
    form.date_from = '';
    form.date_to = '';
    form.min_amount = '';
    form.max_amount = '';
    form.sort_field = '';
    form.sort_direction = 'desc';
    applyFilters();
}
</script> 
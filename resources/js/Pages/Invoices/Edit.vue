<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    invoice: Object,
    patients: Array,
    billingCodes: Array,
    statuses: Object,
});

// Initialize the invoice form with existing data
const form = useForm({
    invoice_date: props.invoice.invoice_date,
    due_date: props.invoice.due_date,
    status: props.invoice.status,
    notes: props.invoice.notes || '',
    items: props.invoice.items.map(item => ({
        id: item.id,
        billing_code_id: item.billing_code_id,
        description: item.description,
        quantity: item.quantity,
        unit_price: item.unit_price,
    })),
});

// Add a new line item to the invoice
function addItem() {
    form.items.push({
        id: null,
        billing_code_id: '',
        description: '',
        quantity: 1,
        unit_price: 0,
    });
}

// Remove an item from the invoice
function removeItem(index) {
    form.items.splice(index, 1);
    if (form.items.length === 0) {
        addItem(); // Always keep at least one item
    }
}

// Calculate subtotal for an item
function calculateItemTotal(item) {
    return (parseFloat(item.quantity) * parseFloat(item.unit_price)).toFixed(2);
}

// Calculate invoice total
const invoiceTotal = computed(() => {
    return form.items.reduce((total, item) => {
        return total + parseFloat(item.quantity) * parseFloat(item.unit_price);
    }, 0).toFixed(2);
});

// Handle billing code selection
function onBillingCodeSelected(item, billingCodeId) {
    const selectedCode = props.billingCodes.find(code => code.id === parseInt(billingCodeId));
    if (selectedCode) {
        item.billing_code_id = selectedCode.id;
        item.description = selectedCode.description;
        item.unit_price = selectedCode.default_price;
    }
}

// Submit the form
function submit() {
    form.put(route('invoices.update', props.invoice.id));
}
</script>

<template>
    <Head title="Edit Invoice" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Invoice #{{ invoice.invoice_number }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <!-- Invoice Info -->
                            <div class="mb-8 bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Invoice Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Patient Information (Read-only) -->
                                    <div>
                                        <InputLabel value="Patient" />
                                        <div class="mt-1 py-2">
                                            {{ invoice.patient.first_name }} {{ invoice.patient.last_name }}
                                            <span class="text-sm text-gray-500 ml-2">(ID: {{ invoice.patient.patient_id }})</span>
                                        </div>
                                    </div>

                                    <!-- Associated Encounter (Read-only) -->
                                    <div v-if="invoice.encounter">
                                        <InputLabel value="Related Encounter" />
                                        <div class="mt-1 py-2">
                                            {{ new Date(invoice.encounter.encounter_date).toLocaleDateString() }} - 
                                            {{ invoice.encounter.chief_complaint }}
                                        </div>
                                    </div>
                                    <div v-else>
                                        <InputLabel value="Related Encounter" />
                                        <div class="mt-1 py-2 text-gray-500">No associated encounter</div>
                                    </div>

                                    <!-- Invoice Date -->
                                    <div>
                                        <InputLabel for="invoice_date" value="Invoice Date" />
                                        <TextInput
                                            id="invoice_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                            v-model="form.invoice_date"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.invoice_date" />
                                    </div>

                                    <!-- Due Date -->
                                    <div>
                                        <InputLabel for="due_date" value="Due Date" />
                                        <TextInput
                                            id="due_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                            v-model="form.due_date"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.due_date" />
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <InputLabel for="status" value="Status" />
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                        >
                                            <option v-for="(label, value) in statuses" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.status" />
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Items -->
                            <div class="mb-8 bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Invoice Items</h3>
                                
                                <table class="w-full">
                                    <thead>
                                        <tr class="text-left">
                                            <th class="pb-2">Billing Code</th>
                                            <th class="pb-2">Description</th>
                                            <th class="pb-2">Qty</th>
                                            <th class="pb-2">Unit Price</th>
                                            <th class="pb-2">Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in form.items" :key="index" class="border-t">
                                            <td class="py-2 pr-2">
                                                <select
                                                    v-model="item.billing_code_id"
                                                    @change="onBillingCodeSelected(item, item.billing_code_id)"
                                                    class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                                    required
                                                >
                                                    <option value="">Select Code</option>
                                                    <option v-for="code in billingCodes" :key="code.id" :value="code.id">
                                                        {{ code.code }} - {{ code.description }}
                                                    </option>
                                                </select>
                                                <InputError :message="form.errors[`items.${index}.billing_code_id`]" />
                                            </td>
                                            <td class="py-2 pr-2">
                                                <TextInput
                                                    v-model="item.description"
                                                    class="block w-full"
                                                    required
                                                />
                                                <InputError :message="form.errors[`items.${index}.description`]" />
                                            </td>
                                            <td class="py-2 pr-2">
                                                <TextInput
                                                    v-model="item.quantity"
                                                    type="number"
                                                    min="1"
                                                    class="block w-20"
                                                    required
                                                />
                                                <InputError :message="form.errors[`items.${index}.quantity`]" />
                                            </td>
                                            <td class="py-2 pr-2">
                                                <TextInput
                                                    v-model="item.unit_price"
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    class="block w-28"
                                                    required
                                                />
                                                <InputError :message="form.errors[`items.${index}.unit_price`]" />
                                            </td>
                                            <td class="py-2 pr-2">
                                                ${{ calculateItemTotal(item) }}
                                            </td>
                                            <td class="py-2">
                                                <button
                                                    type="button"
                                                    @click="removeItem(index)"
                                                    class="text-red-600 hover:text-red-900"
                                                    :disabled="form.items.length === 1"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="6" class="py-4">
                                                <button
                                                    type="button"
                                                    @click="addItem"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    + Add Item
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="font-bold">
                                            <td colspan="4" class="text-right py-2">Total:</td>
                                            <td class="py-2">${{ invoiceTotal }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Additional Notes -->
                            <div class="mb-8 bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Additional Notes</h3>
                                <textarea
                                    v-model="form.notes"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    placeholder="Add any notes or special instructions for this invoice"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.notes" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('invoices.show', invoice.id)"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 mr-2"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Update Invoice
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    invoice: Object,
});

const showDeleteModal = ref(false);

// Format date strings to local format
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};

// Calculate the invoice total
const invoiceTotal = computed(() => {
    if (!props.invoice.items || !props.invoice.items.length) return 0;
    
    return props.invoice.items.reduce((total, item) => {
        return total + (parseFloat(item.quantity) * parseFloat(item.unit_price));
    }, 0).toFixed(2);
});

// Calculate the amount due
const amountDue = computed(() => {
    const total = parseFloat(invoiceTotal.value);
    const paid = props.invoice.amount_paid || 0;
    return (total - parseFloat(paid)).toFixed(2);
});

// Get status badge class based on invoice status
const getStatusBadgeClass = (status) => {
    const classes = {
        draft: 'bg-gray-200 text-gray-800',
        sent: 'bg-blue-200 text-blue-800',
        paid: 'bg-green-200 text-green-800',
        overdue: 'bg-red-200 text-red-800',
        cancelled: 'bg-orange-200 text-orange-800'
    };
    
    return classes[status] || 'bg-gray-200 text-gray-800';
};

// Confirm delete
const confirmDelete = () => {
    showDeleteModal.value = true;
};

// Delete invoice
const deleteInvoice = () => {
    window.location.href = route('invoices.destroy', props.invoice.id);
};

// Download/print invoice
const printInvoice = () => {
    window.print();
};
</script>

<template>
    <Head :title="'Invoice #' + invoice.invoice_number" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Invoice #{{ invoice.invoice_number }}
                </h2>
                <div class="flex space-x-2">
                    <Link v-if="invoice.status !== 'paid'" :href="route('invoices.edit', invoice.id)">
                        <SecondaryButton>Edit Invoice</SecondaryButton>
                    </Link>
                    <PrimaryButton @click="printInvoice">Print/Download</PrimaryButton>
                    <DangerButton 
                        v-if="invoice.status !== 'paid'"
                        @click="confirmDelete" 
                        class="ml-2"
                    >
                        Delete
                    </DangerButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Invoice Details -->
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="print:block grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                            <!-- Invoice Info -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Invoice Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="font-semibold">Invoice Number:</span> 
                                        {{ invoice.invoice_number }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Status:</span>
                                        <span 
                                            :class="[getStatusBadgeClass(invoice.status), 'px-2 py-1 text-xs rounded-full ml-2']"
                                        >
                                            {{ invoice.status.charAt(0).toUpperCase() + invoice.status.slice(1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="font-semibold">Invoice Date:</span> 
                                        {{ formatDate(invoice.invoice_date) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Due Date:</span> 
                                        {{ formatDate(invoice.due_date) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Patient Info -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Patient Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="font-semibold">Patient:</span> 
                                        {{ invoice.patient.first_name }} {{ invoice.patient.last_name }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Patient ID:</span> 
                                        {{ invoice.patient.patient_id }}
                                    </div>
                                    <div v-if="invoice.patient.email">
                                        <span class="font-semibold">Email:</span> 
                                        {{ invoice.patient.email }}
                                    </div>
                                    <div v-if="invoice.patient.phone">
                                        <span class="font-semibold">Phone:</span> 
                                        {{ invoice.patient.phone }}
                                    </div>
                                </div>
                            </div>

                            <!-- Encounter Info -->
                            <div v-if="invoice.encounter" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Related Encounter</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="font-semibold">Date:</span> 
                                        {{ formatDate(invoice.encounter.encounter_date) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold">Chief Complaint:</span> 
                                        {{ invoice.encounter.chief_complaint }}
                                    </div>
                                    <div>
                                        <Link :href="route('encounters.show', invoice.encounter.id)" class="text-blue-600 hover:text-blue-800">
                                            View Encounter Details
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Items -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-8">
                            <h3 class="text-lg font-medium mb-4">Invoice Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b dark:border-gray-600">
                                            <th class="text-left py-3 px-4">Code</th>
                                            <th class="text-left py-3 px-4">Description</th>
                                            <th class="text-right py-3 px-4">Quantity</th>
                                            <th class="text-right py-3 px-4">Unit Price</th>
                                            <th class="text-right py-3 px-4">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="item in invoice.items" :key="item.id" class="border-b dark:border-gray-600">
                                            <td class="py-3 px-4">{{ item.code }}</td>
                                            <td class="py-3 px-4">{{ item.description }}</td>
                                            <td class="py-3 px-4 text-right">{{ item.quantity }}</td>
                                            <td class="py-3 px-4 text-right">${{ parseFloat(item.unit_price).toFixed(2) }}</td>
                                            <td class="py-3 px-4 text-right">${{ (parseFloat(item.quantity) * parseFloat(item.unit_price)).toFixed(2) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="border-t dark:border-gray-500 font-bold">
                                            <td colspan="4" class="py-3 px-4 text-right">Subtotal:</td>
                                            <td class="py-3 px-4 text-right">${{ invoiceTotal }}</td>
                                        </tr>
                                        <tr v-if="invoice.amount_paid">
                                            <td colspan="4" class="py-3 px-4 text-right">Amount Paid:</td>
                                            <td class="py-3 px-4 text-right">${{ parseFloat(invoice.amount_paid).toFixed(2) }}</td>
                                        </tr>
                                        <tr class="font-bold">
                                            <td colspan="4" class="py-3 px-4 text-right">Amount Due:</td>
                                            <td class="py-3 px-4 text-right">${{ amountDue }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div v-if="invoice.notes" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-8">
                            <h3 class="text-lg font-medium mb-4">Notes</h3>
                            <p class="whitespace-pre-line">{{ invoice.notes }}</p>
                        </div>

                        <!-- Payments -->
                        <div v-if="invoice.payments && invoice.payments.length" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-8">
                            <h3 class="text-lg font-medium mb-4">Payment History</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr class="border-b dark:border-gray-600">
                                            <th class="text-left py-3 px-4">Date</th>
                                            <th class="text-left py-3 px-4">Method</th>
                                            <th class="text-left py-3 px-4">Reference</th>
                                            <th class="text-right py-3 px-4">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="payment in invoice.payments" :key="payment.id" class="border-b dark:border-gray-600">
                                            <td class="py-3 px-4">{{ formatDate(payment.payment_date) }}</td>
                                            <td class="py-3 px-4">{{ payment.payment_method }}</td>
                                            <td class="py-3 px-4">{{ payment.reference_number || '—' }}</td>
                                            <td class="py-3 px-4 text-right">${{ parseFloat(payment.amount).toFixed(2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-4 print:hidden">
                            <Link :href="route('invoices.index')" class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600">
                                Back to Invoices
                            </Link>
                            <Link v-if="invoice.status !== 'paid'" :href="route('payments.create', { invoice_id: invoice.id })" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Record Payment
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Are you sure you want to delete this invoice?
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    This action cannot be undone. All invoice data will be permanently deleted.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showDeleteModal = false" class="mr-2">
                        Cancel
                    </SecondaryButton>
                    <DangerButton @click="deleteInvoice">
                        Delete Invoice
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
@media print {
    header, footer, .print\:hidden {
        display: none !important;
    }
    
    .print\:block {
        display: block !important;
    }
    
    /* Ensure the invoice content spans the full page for printing */
    .max-w-7xl {
        max-width: 100% !important;
    }
    
    /* Remove shadows and adjust background for printing */
    .shadow-sm {
        box-shadow: none !important;
    }
    
    .bg-gray-50, .bg-white, .dark\:bg-gray-700, .dark\:bg-gray-800 {
        background-color: white !important;
        color: black !important;
    }
    
    /* Add page breaks where appropriate */
    .mb-8 {
        margin-bottom: 1rem !important;
        page-break-inside: avoid;
    }
}
</style>
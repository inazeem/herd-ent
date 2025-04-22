<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    billingCode: Object,
});

const showDeleteModal = ref(false);

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const deleteBillingCode = () => {
    // Redirect to the delete route
    window.location.href = route('billing-codes.destroy', props.billingCode.id);
};
</script>

<template>
    <Head :title="'Billing Code: ' + billingCode.code" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Billing Code: {{ billingCode.code }}
                </h2>
                <div class="flex space-x-2" v-if="$page.props.can.manage_billing_codes">
                    <Link :href="route('billing-codes.edit', billingCode.id)">
                        <PrimaryButton>Edit</PrimaryButton>
                    </Link>
                    <DangerButton @click="confirmDelete">Delete</DangerButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Code Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Code Details</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <span class="font-semibold block text-sm text-gray-600 dark:text-gray-400">Code Type</span>
                                        <span 
                                            :class="{
                                                'px-2 py-1 rounded text-xs font-medium mt-1 inline-block': true,
                                                'bg-blue-100 text-blue-800': billingCode.code_type === 'CPT',
                                                'bg-green-100 text-green-800': billingCode.code_type === 'ICD-10',
                                                'bg-amber-100 text-amber-800': billingCode.code_type === 'HCPCS'
                                            }"
                                        >
                                            {{ billingCode.code_type }}
                                        </span>
                                    </div>
                                    
                                    <div>
                                        <span class="font-semibold block text-sm text-gray-600 dark:text-gray-400">Code</span>
                                        <span class="text-lg font-medium">{{ billingCode.code }}</span>
                                    </div>
                                    
                                    <div>
                                        <span class="font-semibold block text-sm text-gray-600 dark:text-gray-400">Description</span>
                                        <span>{{ billingCode.description }}</span>
                                    </div>
                                    
                                    <div>
                                        <span class="font-semibold block text-sm text-gray-600 dark:text-gray-400">Price</span>
                                        <span class="text-lg">${{ billingCode.price !== undefined && billingCode.price !== null ? parseFloat(billingCode.price).toFixed(2) : '0.00' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Additional Information</h3>
                                
                                <div class="space-y-4">
                                    <div v-if="billingCode.category">
                                        <span class="font-semibold block text-sm text-gray-600 dark:text-gray-400">Category</span>
                                        <span>{{ billingCode.category }}</span>
                                    </div>
                                    
                                    <div v-if="billingCode.notes">
                                        <span class="font-semibold block text-sm text-gray-600 dark:text-gray-400">Notes</span>
                                        <p class="whitespace-pre-line">{{ billingCode.notes }}</p>
                                    </div>
                                    
                                    <div>
                                        <span class="font-semibold block text-sm text-gray-600 dark:text-gray-400">Created</span>
                                        <span>{{ new Date(billingCode.created_at).toLocaleDateString() }}</span>
                                    </div>
                                    
                                    <div>
                                        <span class="font-semibold block text-sm text-gray-600 dark:text-gray-400">Last Updated</span>
                                        <span>{{ new Date(billingCode.updated_at).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Usage Information (if available) -->
                        <div v-if="billingCode.usage_count" class="mt-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <h3 class="text-lg font-medium mb-4 border-b pb-2">Usage Statistics</h3>
                            
                            <div class="space-y-2">
                                <p>This code has been used in <span class="font-semibold">{{ billingCode.usage_count }}</span> invoices.</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end mt-6">
                            <Link :href="route('billing-codes.index')" class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600">
                                Back to Billing Codes
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
                    Are you sure you want to delete this billing code?
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    This action cannot be undone. If this code is used in any invoices, those references will be preserved.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showDeleteModal = false" class="mr-2">
                        Cancel
                    </SecondaryButton>
                    <DangerButton @click="deleteBillingCode">
                        Delete Billing Code
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
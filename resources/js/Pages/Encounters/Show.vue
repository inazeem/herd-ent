<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    encounter: Object,
});

// Format date helper function
const formatDate = (dateString) => {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        const options = { month: 'short', day: 'numeric', year: 'numeric' };
        return date.toLocaleDateString('en-US', options);
    } catch (e) {
        return dateString;
    }
};

// Get status badge class
const getStatusClass = (status) => {
    const classes = {
        'in-progress': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'completed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'billed': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'
    };
    return classes[status] || classes['in-progress'];
};

// Format status display
const formatStatus = (status) => {
    const statusMap = {
        'in-progress': 'In Progress',
        'completed': 'Completed',
        'billed': 'Billed'
    };
    return statusMap[status] || 'Unknown';
};
</script>

<template>
    <Head :title="`Encounter - ${formatDate(encounter.encounter_date)}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Encounter Details
                </h2>
                <div class="flex space-x-2">
                    <Link
                        v-if="$page.props.can.edit_encounters && encounter.status !== 'billed'"
                        :href="route('encounters.edit', encounter.id)"
                        class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700"
                    >
                        Edit Encounter
                    </Link>
                    <Link
                        v-if="$page.props.can.create_invoices && !encounter.invoice && encounter.status === 'completed'"
                        :href="route('invoices.create') + '?encounter_id=' + encounter.id"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                    >
                        Create Invoice
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Encounter Status Banner -->
                        <div class="mb-6 bg-blue-50 dark:bg-blue-900 p-4 rounded-md flex justify-between items-center">
                            <div>
                                <p class="font-medium text-lg">
                                    Encounter: {{ formatDate(encounter.encounter_date) }}
                                </p>
                                <p class="text-sm">
                                    Chief Complaint: {{ encounter.chief_complaint }}
                                </p>
                            </div>
                            <span :class="[getStatusClass(encounter.status), 'px-3 py-1 rounded-full text-sm font-medium']">
                                {{ formatStatus(encounter.status) }}
                            </span>
                        </div>
                        
                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Patient Information</h3>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Patient:</p>
                                        <p>
                                            <Link 
                                                :href="route('patients.show', encounter.patient.id)"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900"
                                            >
                                                {{ encounter.patient.first_name }} {{ encounter.patient.last_name }}
                                            </Link>
                                        </p>
                                    </div>
                                    <div v-if="encounter.appointment" class="grid grid-cols-2">
                                        <p class="font-medium">Associated Appointment:</p>
                                        <p>
                                            <Link 
                                                :href="route('appointments.show', encounter.appointment.id)"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900"
                                            >
                                                {{ formatDate(encounter.appointment.date) }} - {{ encounter.appointment.appointment_type }}
                                            </Link>
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Clinician:</p>
                                        <p>{{ encounter.clinician?.name || 'Not assigned' }}</p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Date:</p>
                                        <p>{{ formatDate(encounter.encounter_date) }}</p>
                                    </div>
                                    <div v-if="encounter.created_at !== encounter.updated_at" class="grid grid-cols-2">
                                        <p class="font-medium">Last Updated:</p>
                                        <p>{{ formatDate(encounter.updated_at) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-if="encounter.invoice" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Billing Information</h3>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Invoice Number:</p>
                                        <p>
                                            <Link 
                                                :href="route('invoices.show', encounter.invoice.id)"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900"
                                            >
                                                {{ encounter.invoice.invoice_number }}
                                            </Link>
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Amount:</p>
                                        <p>${{ encounter.invoice.total.toFixed(2) }}</p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Status:</p>
                                        <p>{{ encounter.invoice.status }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SOAP Notes -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-6">
                            <h3 class="text-lg font-medium mb-4 border-b pb-2">SOAP Notes</h3>
                            
                            <div class="mb-6">
                                <h4 class="font-medium mb-2">Subjective (Patient's Account):</h4>
                                <p class="whitespace-pre-line bg-white dark:bg-gray-800 p-3 rounded">{{ encounter.subjective }}</p>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="font-medium mb-2">Objective (Clinical Observations):</h4>
                                <p class="whitespace-pre-line bg-white dark:bg-gray-800 p-3 rounded">{{ encounter.objective }}</p>
                            </div>
                            
                            <div class="mb-6">
                                <h4 class="font-medium mb-2">Assessment (Diagnosis):</h4>
                                <p class="whitespace-pre-line bg-white dark:bg-gray-800 p-3 rounded">{{ encounter.assessment }}</p>
                            </div>
                            
                            <div>
                                <h4 class="font-medium mb-2">Plan (Treatment Plan):</h4>
                                <p class="whitespace-pre-line bg-white dark:bg-gray-800 p-3 rounded">{{ encounter.plan }}</p>
                            </div>
                        </div>
                        
                        <!-- Specialized Examinations -->
                        <div v-if="encounter.ear_exam_performed || encounter.nasal_exam_performed || encounter.throat_exam_performed" 
                             class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-6">
                            <h3 class="text-lg font-medium mb-4 border-b pb-2">Specialized Examinations</h3>
                            
                            <div v-if="encounter.ear_exam_performed" class="mb-6">
                                <h4 class="font-medium mb-2">Ear Examination:</h4>
                                <p class="whitespace-pre-line bg-white dark:bg-gray-800 p-3 rounded">{{ encounter.ear_exam_notes || 'Examination performed, no specific findings noted.' }}</p>
                            </div>
                            
                            <div v-if="encounter.nasal_exam_performed" class="mb-6">
                                <h4 class="font-medium mb-2">Nasal Examination:</h4>
                                <p class="whitespace-pre-line bg-white dark:bg-gray-800 p-3 rounded">{{ encounter.nasal_exam_notes || 'Examination performed, no specific findings noted.' }}</p>
                            </div>
                            
                            <div v-if="encounter.throat_exam_performed">
                                <h4 class="font-medium mb-2">Throat Examination:</h4>
                                <p class="whitespace-pre-line bg-white dark:bg-gray-800 p-3 rounded">{{ encounter.throat_exam_notes || 'Examination performed, no specific findings noted.' }}</p>
                            </div>
                        </div>
                        
                        <!-- Additional Notes -->
                        <div v-if="encounter.additional_notes" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-6">
                            <h3 class="text-lg font-medium mb-4 border-b pb-2">Additional Notes</h3>
                            <p class="whitespace-pre-line bg-white dark:bg-gray-800 p-3 rounded">{{ encounter.additional_notes }}</p>
                        </div>
                        
                        <!-- Files -->
                        <div v-if="encounter.files && encounter.files.length > 0" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <h3 class="text-lg font-medium mb-4 border-b pb-2">Files & Documents</h3>
                            <ul class="space-y-2">
                                <li v-for="file in encounter.files" :key="file.id" class="flex justify-between items-center">
                                    <span>{{ file.name }}</span>
                                    <div>
                                        <a :href="route('files.download', file.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-3">
                                            Download
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-between items-center">
                    <Link 
                        :href="route('encounters.index')"
                        class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600"
                    >
                        Back to Encounters
                    </Link>
                    
                    <div class="flex space-x-2">
                        <Link 
                            v-if="encounter.patient"
                            :href="route('patients.show', encounter.patient.id)"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                        >
                            View Patient Profile
                        </Link>
                        
                        <Link 
                            v-if="encounter.status === 'in-progress' && $page.props.can.edit_encounters"
                            :href="`${route('encounters.edit', encounter.id)}?complete=true`"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                        >
                            Mark as Completed
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
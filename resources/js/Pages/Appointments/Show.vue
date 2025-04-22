<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    appointment: Object,
});

// Format date helper function using native JavaScript
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

// Format time helper function
const formatTime = (timeString) => {
    if (!timeString) return '';
    try {
        const time = new Date(`2000-01-01T${timeString}`);
        return time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        return timeString;
    }
};

// Get status badge class
const getStatusClass = (status) => {
    const classes = {
        'scheduled': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'confirmed': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'completed': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300', 
        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        'no-show': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300'
    };
    return classes[status] || classes.scheduled;
};

// Format status display
const formatStatus = (status) => {
    const statusMap = {
        'scheduled': 'Scheduled',
        'confirmed': 'Confirmed',
        'completed': 'Completed',
        'cancelled': 'Cancelled',
        'no-show': 'No-show'
    };
    return statusMap[status] || 'Unknown';
};
</script>

<template>
    <Head :title="`Appointment - ${formatDate(appointment.date)}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Appointment Details
                </h2>
                <div class="flex space-x-2">
                    <Link
                        v-if="$page.props.can.edit_appointments"
                        :href="route('appointments.edit', appointment.id)"
                        class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700"
                    >
                        Edit Appointment
                    </Link>
                    <Link
                        v-if="$page.props.can.create_encounters && appointment.status !== 'cancelled' && appointment.status !== 'no-show'"
                        :href="route('encounters.create') + '?appointment_id=' + appointment.id"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        Create Encounter
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Appointment Status Banner -->
                        <div class="mb-6 bg-blue-50 dark:bg-blue-900 p-4 rounded-md flex justify-between items-center">
                            <p class="font-medium text-lg">
                                Appointment: {{ formatDate(appointment.date) }} at {{ formatTime(appointment.start_time) }}
                            </p>
                            <span :class="[getStatusClass(appointment.status), 'px-3 py-1 rounded-full text-sm font-medium']">
                                {{ formatStatus(appointment.status) }}
                            </span>
                        </div>
                        
                        <!-- Appointment Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Basic Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Basic Information</h3>
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Patient:</p>
                                        <p>
                                            <Link 
                                                :href="route('patients.show', appointment.patient.id)"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900"
                                            >
                                                {{ appointment.patient.first_name }} {{ appointment.patient.last_name }}
                                            </Link>
                                        </p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Clinician:</p>
                                        <p>{{ appointment.clinician?.name || 'Not assigned' }}</p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Date:</p>
                                        <p>{{ formatDate(appointment.date) }}</p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Time:</p>
                                        <p>{{ formatTime(appointment.start_time) }} - {{ formatTime(appointment.end_time) }}</p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Duration:</p>
                                        <p>{{ appointment.duration || '30 minutes' }}</p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Type:</p>
                                        <p>{{ appointment.appointment_type }}</p>
                                    </div>
                                    <div class="grid grid-cols-2">
                                        <p class="font-medium">Created by:</p>
                                        <p>{{ appointment.creator?.name || 'System' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Reason & Notes -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Reason & Notes</h3>
                                <div class="space-y-6">
                                    <div>
                                        <h4 class="font-medium mb-2">Reason for Visit:</h4>
                                        <p class="whitespace-pre-line">{{ appointment.reason || 'No reason provided' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="font-medium mb-2">Additional Notes:</h4>
                                        <p class="whitespace-pre-line">{{ appointment.notes || 'No notes recorded' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reminders Information (if applicable) -->
                        <div v-if="appointment.reminder_sent" class="mt-8 bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <h3 class="text-lg font-medium mb-4 border-b pb-2">Reminder Information</h3>
                            <div class="space-y-3">
                                <div class="grid grid-cols-2">
                                    <p class="font-medium">Reminder sent:</p>
                                    <p>Yes, on {{ formatDate(appointment.reminder_sent_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Encounter Section -->
                <div v-if="appointment.encounter" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Associated Encounter</h3>
                            <Link 
                                :href="route('encounters.show', appointment.encounter.id)"
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-900"
                            >
                                View Full Encounter
                            </Link>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <div class="space-y-3">
                                <div class="grid grid-cols-1 md:grid-cols-2">
                                    <div>
                                        <p class="font-medium">Status:</p>
                                        <span :class="{
                                            'px-2 py-1 rounded-full text-xs font-medium': true,
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': appointment.encounter.status === 'in-progress',
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': appointment.encounter.status === 'completed',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': appointment.encounter.status === 'billed',
                                        }">
                                            {{ appointment.encounter.status === 'in-progress' ? 'In Progress' : 
                                            appointment.encounter.status === 'completed' ? 'Completed' : 'Billed' }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium">Date:</p>
                                        <p>{{ formatDate(appointment.encounter.created_at) }}</p>
                                    </div>
                                </div>
                                <div>
                                    <p class="font-medium">Chief Complaint:</p>
                                    <p>{{ appointment.encounter.chief_complaint }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-between">
                    <Link 
                        :href="route('appointments.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-600 focus:bg-gray-400 dark:focus:bg-gray-600 active:bg-gray-500 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        Back to Appointments
                    </Link>
                    
                    <Link 
                        v-if="appointment.patient"
                        :href="route('patients.show', appointment.patient.id)"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                    >
                        View Patient Profile
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
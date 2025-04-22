<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    patient: Object,
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
</script>

<template>
    <Head :title="patient.first_name + ' ' + patient.last_name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Patient: {{ patient.first_name }} {{ patient.last_name }}
                </h2>
                <div class="flex flex-wrap gap-2">
                    <Link
                        v-if="$page.props.can.edit_patients"
                        :href="route('patients.edit', patient.id)"
                        class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-700"
                    >
                        Edit Patient
                    </Link>
                    <Link
                        v-if="$page.props.can.create_appointments"
                        :href="route('appointments.create') + '?patient_id=' + patient.id"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                    >
                        Add Appointment
                    </Link>
                    <Link
                        v-if="$page.props.can.create_encounters"
                        :href="route('encounters.create') + '?patient_id=' + patient.id"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                    >
                        New Encounter
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                        <!-- Patient ID Banner -->
                        <div class="mb-6 bg-blue-50 dark:bg-blue-900 p-4 rounded-md">
                            <p class="font-medium text-lg">Patient ID: {{ patient.patient_id }}</p>
                        </div>
                        
                        <!-- Patient Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-8">
                            <!-- Personal Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Personal Information</h3>
                                <div class="space-y-3">
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Full Name:</p>
                                        <p>{{ patient.first_name }} {{ patient.last_name }}</p>
                                    </div>
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Date of Birth:</p>
                                        <p>{{ formatDate(patient.date_of_birth) }} ({{ new Date().getFullYear() - new Date(patient.date_of_birth).getFullYear() }} years)</p>
                                    </div>
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Gender:</p>
                                        <p>{{ patient.gender === 'male' ? 'Male' : patient.gender === 'female' ? 'Female' : 'Other' }}</p>
                                    </div>
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Email:</p>
                                        <p>{{ patient.email || 'Not provided' }}</p>
                                    </div>
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Phone:</p>
                                        <p>{{ patient.phone }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Address Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Address</h3>
                                <div class="space-y-3">
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Street:</p>
                                        <p>{{ patient.address || 'Not provided' }}</p>
                                    </div>
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">City:</p>
                                        <p>{{ patient.city || 'Not provided' }}</p>
                                    </div>
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">State/Province:</p>
                                        <p>{{ patient.state || 'Not provided' }}</p>
                                    </div>
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Postal Code:</p>
                                        <p>{{ patient.postal_code || 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Emergency Contact -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Emergency Contact</h3>
                                <div class="space-y-3">
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Name:</p>
                                        <p>{{ patient.emergency_contact_name || 'Not provided' }}</p>
                                    </div>
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Phone:</p>
                                        <p>{{ patient.emergency_contact_phone || 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Insurance Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4 border-b pb-2">Insurance Information</h3>
                                <div class="space-y-3">
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Provider:</p>
                                        <p>{{ patient.insurance_provider || 'Not provided' }}</p>
                                    </div>
                                    <div class="sm:grid sm:grid-cols-2">
                                        <p class="font-medium">Policy Number:</p>
                                        <p>{{ patient.insurance_policy_number || 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Medical Information -->
                        <div class="mt-8 bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <h3 class="text-lg font-medium mb-4 border-b pb-2">Medical Information</h3>
                            <div class="space-y-6">
                                <div>
                                    <h4 class="font-medium mb-2">Medical History:</h4>
                                    <p class="whitespace-pre-line">{{ patient.medical_history || 'None recorded' }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium mb-2">Allergies:</h4>
                                    <p class="whitespace-pre-line">{{ patient.allergies || 'None recorded' }}</p>
                                </div>
                                <div>
                                    <h4 class="font-medium mb-2">Current Medications:</h4>
                                    <p class="whitespace-pre-line">{{ patient.current_medications || 'None recorded' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Appointments Section -->
                <div v-if="patient.appointments && patient.appointments.length > 0" class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4">Appointments</h3>
                        
                        <!-- Desktop view - Table (hidden on small screens) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Clinician</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="appointment in patient.appointments" :key="appointment.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(appointment.date) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ appointment.start_time }} - {{ appointment.end_time }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ appointment.clinician?.name || 'Not assigned' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ appointment.appointment_type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'px-2 py-1 rounded-full text-xs font-medium': true,
                                                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': appointment.status === 'scheduled',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': appointment.status === 'confirmed',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': appointment.status === 'completed',
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': appointment.status === 'cancelled' || appointment.status === 'no-show',
                                            }">
                                                {{ appointment.status === 'scheduled' ? 'Scheduled' : 
                                                   appointment.status === 'confirmed' ? 'Confirmed' :
                                                   appointment.status === 'completed' ? 'Completed' : 
                                                   appointment.status === 'cancelled' ? 'Cancelled' : 'No-show' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('appointments.show', appointment.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-3">View</Link>
                                            <Link v-if="$page.props.can.edit_appointments" :href="route('appointments.edit', appointment.id)" class="text-amber-600 dark:text-amber-400 hover:text-amber-900 mr-3">Edit</Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile view - Cards (hidden on medium and larger screens) -->
                        <div class="block md:hidden">
                            <div v-for="appointment in patient.appointments" :key="appointment.id" 
                                 class="mb-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-semibold">{{ formatDate(appointment.date) }}</p>
                                        <p class="text-sm">{{ appointment.start_time }} - {{ appointment.end_time }}</p>
                                    </div>
                                    <span :class="{
                                        'px-2 py-1 rounded-full text-xs font-medium': true,
                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300': appointment.status === 'scheduled',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': appointment.status === 'confirmed',
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': appointment.status === 'completed',
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300': appointment.status === 'cancelled' || appointment.status === 'no-show',
                                    }">
                                        {{ appointment.status === 'scheduled' ? 'Scheduled' : 
                                           appointment.status === 'confirmed' ? 'Confirmed' :
                                           appointment.status === 'completed' ? 'Completed' : 
                                           appointment.status === 'cancelled' ? 'Cancelled' : 'No-show' }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <p><span class="font-medium">Type:</span> {{ appointment.appointment_type }}</p>
                                    <p><span class="font-medium">Clinician:</span> {{ appointment.clinician?.name || 'Not assigned' }}</p>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <Link :href="route('appointments.show', appointment.id)" class="px-3 py-1 text-xs bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300 rounded">View</Link>
                                    <Link v-if="$page.props.can.edit_appointments" :href="route('appointments.edit', appointment.id)" class="px-3 py-1 text-xs bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-300 rounded">Edit</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Encounters Section -->
                <div v-if="patient.encounters && patient.encounters.length > 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4">Encounters</h3>
                        
                        <!-- Desktop view - Table (hidden on small screens) -->
                        <div class="hidden md:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Clinician</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Chief Complaint</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="encounter in patient.encounters" :key="encounter.id">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(encounter.created_at) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ encounter.clinician?.name || 'Not assigned' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ encounter.chief_complaint }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="{
                                                'px-2 py-1 rounded-full text-xs font-medium': true,
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': encounter.status === 'in-progress',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': encounter.status === 'completed',
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': encounter.status === 'billed',
                                            }">
                                                {{ encounter.status === 'in-progress' ? 'In Progress' : 
                                                   encounter.status === 'completed' ? 'Completed' : 'Billed' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('encounters.show', encounter.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-3">View</Link>
                                            <Link v-if="$page.props.can.edit_encounters" :href="route('encounters.edit', encounter.id)" class="text-amber-600 dark:text-amber-400 hover:text-amber-900 mr-3">Edit</Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Mobile view - Cards (hidden on medium and larger screens) -->
                        <div class="block md:hidden">
                            <div v-for="encounter in patient.encounters" :key="encounter.id" 
                                 class="mb-4 bg-gray-50 dark:bg-gray-700 rounded-lg shadow p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-semibold">{{ formatDate(encounter.created_at) }}</p>
                                        <p class="truncate max-w-[200px]">{{ encounter.chief_complaint }}</p>
                                    </div>
                                    <span :class="{
                                        'px-2 py-1 rounded-full text-xs font-medium': true,
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300': encounter.status === 'in-progress',
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': encounter.status === 'completed',
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': encounter.status === 'billed',
                                    }">
                                        {{ encounter.status === 'in-progress' ? 'In Progress' : 
                                           encounter.status === 'completed' ? 'Completed' : 'Billed' }}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <p><span class="font-medium">Clinician:</span> {{ encounter.clinician?.name || 'Not assigned' }}</p>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <Link :href="route('encounters.show', encounter.id)" class="px-3 py-1 text-xs bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300 rounded">View</Link>
                                    <Link v-if="$page.props.can.edit_encounters" :href="route('encounters.edit', encounter.id)" class="px-3 py-1 text-xs bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-300 rounded">Edit</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
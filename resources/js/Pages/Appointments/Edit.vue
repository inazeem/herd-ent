<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    appointment: Object,
    patients: Object,
    clinicians: Object,
});

const form = useForm({
    patient_id: props.appointment.patient_id,
    clinician_id: props.appointment.clinician_id,
    date: props.appointment.date,
    start_time: props.appointment.start_time,
    end_time: props.appointment.end_time,
    appointment_type: props.appointment.appointment_type,
    status: props.appointment.status,
    reason: props.appointment.reason,
    notes: props.appointment.notes,
});

const appointmentTypes = [
    'Initial Consultation',
    'Follow-up',
    'Annual Check-up',
    'Urgent Care',
    'Procedure',
    'Therapy',
    'Other'
];

const submit = () => {
    form.put(route('appointments.update', props.appointment.id));
};

// Auto-calculate end time based on start time (default to 30 minutes later)
// Only apply this when the user manually changes the start time
watch(() => form.start_time, (newStartTime, oldStartTime) => {
    if (newStartTime && newStartTime !== oldStartTime) {
        try {
            const startDate = new Date(`2000-01-01T${newStartTime}`);
            startDate.setMinutes(startDate.getMinutes() + 30);
            const hours = startDate.getHours().toString().padStart(2, '0');
            const minutes = startDate.getMinutes().toString().padStart(2, '0');
            form.end_time = `${hours}:${minutes}`;
        } catch (e) {
            // Invalid time format, don't auto-set end time
        }
    }
});
</script>

<template>
    <Head title="Edit Appointment" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Appointment</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Appointment Details -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Appointment Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Patient Selection -->
                                    <div>
                                        <InputLabel for="patient_id" value="Patient" />
                                        <select
                                            id="patient_id"
                                            v-model="form.patient_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                        >
                                            <option value="">Select Patient</option>
                                            <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                                                {{ patient.first_name }} {{ patient.last_name }} ({{ patient.patient_id }})
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.patient_id" />
                                    </div>

                                    <!-- Clinician Selection -->
                                    <div>
                                        <InputLabel for="clinician_id" value="Clinician" />
                                        <select
                                            id="clinician_id"
                                            v-model="form.clinician_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                        >
                                            <option value="">Select Clinician</option>
                                            <option v-for="clinician in clinicians" :key="clinician.id" :value="clinician.id">
                                                {{ clinician.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.clinician_id" />
                                    </div>

                                    <!-- Date -->
                                    <div>
                                        <InputLabel for="date" value="Date" />
                                        <TextInput
                                            id="date"
                                            type="date"
                                            class="mt-1 block w-full"
                                            v-model="form.date"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.date" />
                                    </div>

                                    <!-- Start Time -->
                                    <div>
                                        <InputLabel for="start_time" value="Start Time" />
                                        <TextInput
                                            id="start_time"
                                            type="time"
                                            class="mt-1 block w-full"
                                            v-model="form.start_time"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.start_time" />
                                    </div>

                                    <!-- End Time -->
                                    <div>
                                        <InputLabel for="end_time" value="End Time" />
                                        <TextInput
                                            id="end_time"
                                            type="time"
                                            class="mt-1 block w-full"
                                            v-model="form.end_time"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.end_time" />
                                    </div>

                                    <!-- Appointment Type -->
                                    <div>
                                        <InputLabel for="appointment_type" value="Appointment Type" />
                                        <select
                                            id="appointment_type"
                                            v-model="form.appointment_type"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                        >
                                            <option value="">Select Type</option>
                                            <option v-for="type in appointmentTypes" :key="type" :value="type">
                                                {{ type }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.appointment_type" />
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
                                            <option value="scheduled">Scheduled</option>
                                            <option value="confirmed">Confirmed</option>
                                            <option value="cancelled">Cancelled</option>
                                            <option value="completed">Completed</option>
                                            <option value="no-show">No-show</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.status" />
                                    </div>
                                </div>
                            </div>

                            <!-- Reason and Notes -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Reason & Notes</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Reason -->
                                    <div>
                                        <InputLabel for="reason" value="Reason for Visit" />
                                        <textarea
                                            id="reason"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.reason"
                                            rows="3"
                                            required
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.reason" />
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <InputLabel for="notes" value="Additional Notes" />
                                        <textarea
                                            id="notes"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.notes"
                                            rows="3"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.notes" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('appointments.show', appointment.id)"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 mr-2"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Update Appointment
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
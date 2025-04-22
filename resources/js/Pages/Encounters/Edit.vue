<script setup>
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    encounter: Object,
    patients: Object,
    clinicians: Object,
    appointments: Object,
    complete: Boolean,
});

const form = useForm({
    patient_id: props.encounter.patient_id,
    appointment_id: props.encounter.appointment_id || '',
    clinician_id: props.encounter.clinician_id,
    encounter_date: props.encounter.encounter_date,
    chief_complaint: props.encounter.chief_complaint,
    subjective: props.encounter.subjective,
    objective: props.encounter.objective,
    assessment: props.encounter.assessment,
    plan: props.encounter.plan,
    ear_exam_performed: props.encounter.ear_exam_performed,
    ear_exam_notes: props.encounter.ear_exam_notes || '',
    nasal_exam_performed: props.encounter.nasal_exam_performed,
    nasal_exam_notes: props.encounter.nasal_exam_notes || '',
    throat_exam_performed: props.encounter.throat_exam_performed,
    throat_exam_notes: props.encounter.throat_exam_notes || '',
    additional_notes: props.encounter.additional_notes || '',
    status: props.complete ? 'completed' : props.encounter.status
});

const filteredAppointments = ref([]);

// Update filtered appointments for this patient
const updateFilteredAppointments = () => {
    if (props.appointments) {
        filteredAppointments.value = props.appointments.filter(
            appointment => appointment.patient_id === form.patient_id
        );
    } else {
        filteredAppointments.value = [];
    }
};

// Initial update
updateFilteredAppointments();

// If the "complete" parameter is present, automatically handle completion
const autoComplete = ref(props.complete);
if (autoComplete.value) {
    form.status = 'completed';
}

const submit = () => {
    form.put(route('encounters.update', props.encounter.id));
};
</script>

<template>
    <Head title="Edit Encounter" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Edit Encounter</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Basic Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Basic Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Patient Selection (readonly if there's an invoice) -->
                                    <div>
                                        <InputLabel for="patient_id" value="Patient" />
                                        <select
                                            id="patient_id"
                                            v-model="form.patient_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            required
                                            :disabled="encounter.invoice || encounter.status === 'billed'"
                                        >
                                            <option value="">Select Patient</option>
                                            <option v-for="patient in patients" :key="patient.id" :value="patient.id">
                                                {{ patient.first_name }} {{ patient.last_name }} ({{ patient.patient_id }})
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.patient_id" />
                                    </div>

                                    <!-- Associated Appointment (Optional) -->
                                    <div>
                                        <InputLabel for="appointment_id" value="Appointment (Optional)" />
                                        <select
                                            id="appointment_id"
                                            v-model="form.appointment_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            :disabled="encounter.invoice || encounter.status === 'billed'"
                                        >
                                            <option value="">No Associated Appointment</option>
                                            <option v-for="appointment in filteredAppointments" :key="appointment.id" :value="appointment.id">
                                                {{ new Date(appointment.date).toLocaleDateString() }} - {{ appointment.appointment_type }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.appointment_id" />
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

                                    <!-- Encounter Date -->
                                    <div>
                                        <InputLabel for="encounter_date" value="Encounter Date" />
                                        <TextInput
                                            id="encounter_date"
                                            type="date"
                                            class="mt-1 block w-full"
                                            v-model="form.encounter_date"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.encounter_date" />
                                    </div>

                                    <!-- Chief Complaint -->
                                    <div class="md:col-span-2">
                                        <InputLabel for="chief_complaint" value="Chief Complaint" />
                                        <textarea
                                            id="chief_complaint"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.chief_complaint"
                                            rows="2"
                                            required
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.chief_complaint" />
                                    </div>
                                </div>
                            </div>

                            <!-- SOAP Notes -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">SOAP Notes</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Subjective -->
                                    <div>
                                        <InputLabel for="subjective" value="Subjective (Patient's Account)" />
                                        <textarea
                                            id="subjective"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.subjective"
                                            rows="3"
                                            required
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.subjective" />
                                    </div>

                                    <!-- Objective -->
                                    <div>
                                        <InputLabel for="objective" value="Objective (Clinical Observations)" />
                                        <textarea
                                            id="objective"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.objective"
                                            rows="3"
                                            required
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.objective" />
                                    </div>

                                    <!-- Assessment -->
                                    <div>
                                        <InputLabel for="assessment" value="Assessment (Diagnosis)" />
                                        <textarea
                                            id="assessment"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.assessment"
                                            rows="3"
                                            required
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.assessment" />
                                    </div>

                                    <!-- Plan -->
                                    <div>
                                        <InputLabel for="plan" value="Plan (Treatment Plan)" />
                                        <textarea
                                            id="plan"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.plan"
                                            rows="3"
                                            required
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.plan" />
                                    </div>
                                </div>
                            </div>

                            <!-- Specialized Exams -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Specialized Examinations</h3>
                                
                                <!-- Ear Exam -->
                                <div class="mb-6">
                                    <div class="flex items-center mb-2">
                                        <Checkbox id="ear_exam_performed" v-model:checked="form.ear_exam_performed" />
                                        <InputLabel for="ear_exam_performed" value="Ear Examination Performed" class="ml-2" />
                                    </div>
                                    <div v-if="form.ear_exam_performed">
                                        <textarea
                                            id="ear_exam_notes"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.ear_exam_notes"
                                            placeholder="Enter ear examination findings"
                                            rows="2"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.ear_exam_notes" />
                                    </div>
                                </div>
                                
                                <!-- Nasal Exam -->
                                <div class="mb-6">
                                    <div class="flex items-center mb-2">
                                        <Checkbox id="nasal_exam_performed" v-model:checked="form.nasal_exam_performed" />
                                        <InputLabel for="nasal_exam_performed" value="Nasal Examination Performed" class="ml-2" />
                                    </div>
                                    <div v-if="form.nasal_exam_performed">
                                        <textarea
                                            id="nasal_exam_notes"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.nasal_exam_notes"
                                            placeholder="Enter nasal examination findings"
                                            rows="2"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.nasal_exam_notes" />
                                    </div>
                                </div>
                                
                                <!-- Throat Exam -->
                                <div>
                                    <div class="flex items-center mb-2">
                                        <Checkbox id="throat_exam_performed" v-model:checked="form.throat_exam_performed" />
                                        <InputLabel for="throat_exam_performed" value="Throat Examination Performed" class="ml-2" />
                                    </div>
                                    <div v-if="form.throat_exam_performed">
                                        <textarea
                                            id="throat_exam_notes"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.throat_exam_notes"
                                            placeholder="Enter throat examination findings"
                                            rows="2"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.throat_exam_notes" />
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Additional Notes</h3>
                                <div>
                                    <textarea
                                        id="additional_notes"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        v-model="form.additional_notes"
                                        rows="3"
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.additional_notes" />
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Status</h3>
                                <div>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="in-progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option v-if="encounter.status === 'billed'" value="billed">Billed</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.status" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('encounters.show', encounter.id)"
                                    class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600 mr-2"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    {{ autoComplete ? 'Complete Encounter' : 'Update Encounter' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
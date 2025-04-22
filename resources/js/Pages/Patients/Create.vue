<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const form = useForm({
    first_name: '',
    last_name: '',
    date_of_birth: '',
    gender: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    postal_code: '',
    emergency_contact_name: '',
    emergency_contact_phone: '',
    medical_history: '',
    allergies: '',
    current_medications: '',
    insurance_provider: '',
    insurance_policy_number: ''
});

const submit = () => {
    form.post(route('patients.store'));
};
</script>

<template>
    <Head title="Create Patient" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Create Patient</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Section Navigation for Mobile -->
                            <div class="block md:hidden mb-4">
                                <label for="section-nav" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Jump to Section
                                </label>
                                <select 
                                    id="section-nav"
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    onchange="document.getElementById(this.value).scrollIntoView({behavior: 'smooth'})"
                                >
                                    <option value="personal-info">Personal Information</option>
                                    <option value="address-info">Address</option>
                                    <option value="emergency-contact">Emergency Contact</option>
                                    <option value="medical-info">Medical Information</option>
                                    <option value="insurance-info">Insurance Information</option>
                                </select>
                            </div>
                            
                            <!-- Personal Information Section -->
                            <div id="personal-info" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Personal Information</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- First Name -->
                                    <div>
                                        <InputLabel for="first_name" value="First Name" />
                                        <TextInput
                                            id="first_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.first_name"
                                            required
                                            autofocus
                                        />
                                        <InputError class="mt-2" :message="form.errors.first_name" />
                                    </div>

                                    <!-- Last Name -->
                                    <div>
                                        <InputLabel for="last_name" value="Last Name" />
                                        <TextInput
                                            id="last_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.last_name"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.last_name" />
                                    </div>

                                    <!-- Date of Birth -->
                                    <div>
                                        <InputLabel for="date_of_birth" value="Date of Birth" />
                                        <TextInput
                                            id="date_of_birth"
                                            type="date"
                                            class="mt-1 block w-full"
                                            v-model="form.date_of_birth"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.date_of_birth" />
                                    </div>

                                    <!-- Gender -->
                                    <div>
                                        <InputLabel for="gender" value="Gender" />
                                        <select
                                            id="gender"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.gender"
                                            required
                                        >
                                            <option value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.gender" />
                                    </div>

                                    <!-- Email -->
                                    <div>
                                        <InputLabel for="email" value="Email" />
                                        <TextInput
                                            id="email"
                                            type="email"
                                            class="mt-1 block w-full"
                                            v-model="form.email"
                                            inputmode="email"
                                        />
                                        <InputError class="mt-2" :message="form.errors.email" />
                                    </div>

                                    <!-- Phone -->
                                    <div>
                                        <InputLabel for="phone" value="Phone" />
                                        <TextInput
                                            id="phone"
                                            type="tel"
                                            class="mt-1 block w-full"
                                            v-model="form.phone"
                                            required
                                            inputmode="tel"
                                        />
                                        <InputError class="mt-2" :message="form.errors.phone" />
                                    </div>
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div id="address-info" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Address</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Address -->
                                    <div class="sm:col-span-2">
                                        <InputLabel for="address" value="Address" />
                                        <TextInput
                                            id="address"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.address"
                                        />
                                        <InputError class="mt-2" :message="form.errors.address" />
                                    </div>

                                    <!-- City -->
                                    <div>
                                        <InputLabel for="city" value="City" />
                                        <TextInput
                                            id="city"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.city"
                                        />
                                        <InputError class="mt-2" :message="form.errors.city" />
                                    </div>

                                    <!-- State -->
                                    <div>
                                        <InputLabel for="state" value="State/Province" />
                                        <TextInput
                                            id="state"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.state"
                                        />
                                        <InputError class="mt-2" :message="form.errors.state" />
                                    </div>

                                    <!-- Postal Code -->
                                    <div>
                                        <InputLabel for="postal_code" value="Postal Code" />
                                        <TextInput
                                            id="postal_code"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.postal_code"
                                            inputmode="numeric"
                                        />
                                        <InputError class="mt-2" :message="form.errors.postal_code" />
                                    </div>
                                </div>
                            </div>

                            <!-- Emergency Contact Section -->
                            <div id="emergency-contact" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Emergency Contact</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Emergency Contact Name -->
                                    <div>
                                        <InputLabel for="emergency_contact_name" value="Contact Name" />
                                        <TextInput
                                            id="emergency_contact_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.emergency_contact_name"
                                        />
                                        <InputError class="mt-2" :message="form.errors.emergency_contact_name" />
                                    </div>

                                    <!-- Emergency Contact Phone -->
                                    <div>
                                        <InputLabel for="emergency_contact_phone" value="Contact Phone" />
                                        <TextInput
                                            id="emergency_contact_phone"
                                            type="tel"
                                            class="mt-1 block w-full"
                                            v-model="form.emergency_contact_phone"
                                            inputmode="tel"
                                        />
                                        <InputError class="mt-2" :message="form.errors.emergency_contact_phone" />
                                    </div>
                                </div>
                            </div>

                            <!-- Medical Information Section -->
                            <div id="medical-info" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Medical Information</h3>
                                <div class="grid grid-cols-1 gap-4">
                                    <!-- Medical History -->
                                    <div>
                                        <InputLabel for="medical_history" value="Medical History" />
                                        <textarea
                                            id="medical_history"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.medical_history"
                                            rows="3"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.medical_history" />
                                    </div>

                                    <!-- Allergies -->
                                    <div>
                                        <InputLabel for="allergies" value="Allergies" />
                                        <textarea
                                            id="allergies"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.allergies"
                                            rows="2"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.allergies" />
                                    </div>

                                    <!-- Current Medications -->
                                    <div>
                                        <InputLabel for="current_medications" value="Current Medications" />
                                        <textarea
                                            id="current_medications"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            v-model="form.current_medications"
                                            rows="2"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.current_medications" />
                                    </div>
                                </div>
                            </div>

                            <!-- Insurance Information Section -->
                            <div id="insurance-info" class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                <h3 class="text-lg font-medium mb-4">Insurance Information</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Insurance Provider -->
                                    <div>
                                        <InputLabel for="insurance_provider" value="Insurance Provider" />
                                        <TextInput
                                            id="insurance_provider"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.insurance_provider"
                                        />
                                        <InputError class="mt-2" :message="form.errors.insurance_provider" />
                                    </div>

                                    <!-- Insurance Policy Number -->
                                    <div>
                                        <InputLabel for="insurance_policy_number" value="Policy Number" />
                                        <TextInput
                                            id="insurance_policy_number"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.insurance_policy_number"
                                        />
                                        <InputError class="mt-2" :message="form.errors.insurance_policy_number" />
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Create Patient
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
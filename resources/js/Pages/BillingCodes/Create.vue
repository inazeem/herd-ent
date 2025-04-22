<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextArea from '@/Components/TextArea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    codeTypes: Object,
});

const form = useForm({
    code: '',
    description: '',
    code_type: '',
    price: '',
    notes: '',
});

const submit = () => {
    form.post(route('billing-codes.store'));
};
</script>

<template>
    <Head title="Add Billing Code" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Add Billing Code</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="code_type" value="Code Type" />
                                <SelectInput
                                    id="code_type"
                                    v-model="form.code_type"
                                    class="mt-1 block w-full"
                                    required
                                >
                                    <option value="" disabled>Select Code Type</option>
                                    <option v-for="(label, value) in codeTypes" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </SelectInput>
                                <InputError :message="form.errors.code_type" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="code" value="Code" />
                                <TextInput
                                    id="code"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.code"
                                    required
                                />
                                <InputError :message="form.errors.code" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="description" value="Description" />
                                <TextInput
                                    id="description"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.description"
                                    required
                                />
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="price" value="Price ($)" />
                                <TextInput
                                    id="price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="mt-1 block w-full"
                                    v-model="form.price"
                                    required
                                />
                                <InputError :message="form.errors.price" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="notes" value="Notes (Optional)" />
                                <TextArea
                                    id="notes"
                                    class="mt-1 block w-full"
                                    v-model="form.notes"
                                    rows="4"
                                />
                                <InputError :message="form.errors.notes" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton
                                    class="ml-4"
                                    :disabled="form.processing"
                                >
                                    Create Billing Code
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
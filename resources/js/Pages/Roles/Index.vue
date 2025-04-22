<template>
    <Head title="Roles" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Roles</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between mb-6">
                            <div class="flex items-center">
                                <h3 class="text-lg font-semibold">Role List</h3>
                            </div>
                            <Link
                                v-if="$page.props.can && $page.props.can.create_roles"
                                :href="route('roles.create')"
                                class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600"
                            >
                                Create Role
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="text-left font-bold">
                                        <th class="pb-4 pt-6 px-6">Name</th>
                                        <th class="pb-4 pt-6 px-6">Permissions</th>
                                        <th class="pb-4 pt-6 px-6">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="role in roles.data" :key="role.id" class="hover:bg-gray-100">
                                        <td class="border-t px-6 py-4">{{ role.name }}</td>
                                        <td class="border-t px-6 py-4">
                                            <span
                                                v-for="permission in role.permissions"
                                                :key="permission.id"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2 mb-1"
                                            >
                                                {{ permission.name }}
                                            </span>
                                        </td>
                                        <td class="border-t px-6 py-4">
                                            <Link
                                                v-if="$page.props.can && $page.props.can.edit_roles"
                                                :href="route('roles.edit', role.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                v-if="$page.props.can && $page.props.can.delete_roles && role.name !== 'super-admin'"
                                                @click="deleteRole(role)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="roles.data.length === 0">
                                        <td class="border-t px-6 py-4 text-center" colspan="3">No roles found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="roles.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    roles: Object,
});

const deleteRole = (role) => {
    if (window.confirm(`Are you sure you want to delete the "${role.name}" role?`)) {
        router.delete(route('roles.destroy', role.id));
    }
};
</script>
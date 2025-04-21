<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Users</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between mb-6">
                            <div class="flex items-center">
                                <h3 class="text-lg font-semibold">User List</h3>
                            </div>
                            <Link
                                v-if="$page.props.can.create_users"
                                :href="route('users.create')"
                                class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600"
                            >
                                Create User
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full whitespace-nowrap">
                                <thead>
                                    <tr class="text-left font-bold">
                                        <th class="pb-4 pt-6 px-6">Name</th>
                                        <th class="pb-4 pt-6 px-6">Email</th>
                                        <th class="pb-4 pt-6 px-6">Roles</th>
                                        <th class="pb-4 pt-6 px-6">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-100">
                                        <td class="border-t px-6 py-4">{{ user.name }}</td>
                                        <td class="border-t px-6 py-4">{{ user.email }}</td>
                                        <td class="border-t px-6 py-4">
                                            <span
                                                v-for="role in user.roles"
                                                :key="role.id"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mr-2"
                                            >
                                                {{ role.name }}
                                            </span>
                                        </td>
                                        <td class="border-t px-6 py-4">
                                            <Link
                                                v-if="$page.props.can.edit_users"
                                                :href="route('users.edit', user.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                v-if="$page.props.can.delete_users"
                                                @click="deleteUser(user)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="users.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

defineProps({
    users: Object,
});

function deleteUser(user) {
    if (confirm(`Are you sure you want to delete ${user.name}?`)) {
        router.delete(route('users.destroy', user.id));
    }
}
</script> 
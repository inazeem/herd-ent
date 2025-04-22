<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

// Define props to receive data from the controller
const props = defineProps({
    counts: Object,
    revenue: Object,
    todayStats: Object,
    todayAppointments: Array,
    recentInvoices: Array,
    calendarAppointments: Array,
    upcomingAppointments: Object,
    providerPerformance: Array,
    today: String,
    startOfMonth: String,
    endOfMonth: String,
});

// Format currency helper function
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
    }).format(value || 0);
};

// Format date helper function
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

// Format time helper function
const formatTime = (timeString) => {
    if (!timeString) return '';
    try {
        return new Date(`2000-01-01T${timeString}`).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return timeString;
    }
};

// Calculate percentage of growth with proper sign
const formatGrowth = (growth) => {
    if (growth === undefined || growth === null) return '0%';
    const sign = growth >= 0 ? '+' : '';
    return `${sign}${growth}%`;
};
</script>

<template>
    <Head title="Reports Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Reports Dashboard
                </h2>
                <div class="text-sm text-gray-500">
                    <span>{{ formatDate(today) }}</span>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Key Metrics Cards -->
                <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Patients Metric Card -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 rounded-md bg-blue-500 p-3">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <h3 class="text-lg font-medium text-gray-900">Patients</h3>
                                    <div class="mt-1 flex items-baseline">
                                        <p class="text-2xl font-semibold text-gray-900">{{ counts?.patients?.total || 0 }}</p>
                                        <p class="ml-2 text-sm font-medium" :class="{'text-green-600': counts?.patients?.growth >= 0, 'text-red-600': counts?.patients?.growth < 0}">
                                            {{ formatGrowth(counts?.patients?.growth) }}
                                        </p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">{{ counts?.patients?.thisMonth || 0 }} new this month</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appointments Metric Card -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 rounded-md bg-green-500 p-3">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <h3 class="text-lg font-medium text-gray-900">Appointments</h3>
                                    <div class="mt-1 flex items-baseline">
                                        <p class="text-2xl font-semibold text-gray-900">{{ counts?.appointments?.total || 0 }}</p>
                                        <p class="ml-2 text-sm font-medium" :class="{'text-green-600': counts?.appointments?.growth >= 0, 'text-red-600': counts?.appointments?.growth < 0}">
                                            {{ formatGrowth(counts?.appointments?.growth) }}
                                        </p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">{{ counts?.appointments?.thisMonth || 0 }} this month</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Encounters Metric Card -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 rounded-md bg-purple-500 p-3">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <h3 class="text-lg font-medium text-gray-900">Encounters</h3>
                                    <div class="mt-1 flex items-baseline">
                                        <p class="text-2xl font-semibold text-gray-900">{{ counts?.encounters?.total || 0 }}</p>
                                        <p class="ml-2 text-sm font-medium" :class="{'text-green-600': counts?.encounters?.growth >= 0, 'text-red-600': counts?.encounters?.growth < 0}">
                                            {{ formatGrowth(counts?.encounters?.growth) }}
                                        </p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">{{ counts?.encounters?.thisMonth || 0 }} this month</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Metric Card -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 rounded-md bg-yellow-500 p-3">
                                    <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5">
                                    <h3 class="text-lg font-medium text-gray-900">Revenue</h3>
                                    <div class="mt-1 flex items-baseline">
                                        <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(revenue?.total) }}</p>
                                        <p class="ml-2 text-sm font-medium" :class="{'text-green-600': revenue?.growth >= 0, 'text-red-600': revenue?.growth < 0}">
                                            {{ formatGrowth(revenue?.growth) }}
                                        </p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">{{ formatCurrency(revenue?.thisMonth) }} this month</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Overview Section -->
                <div class="mb-8">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Financial Overview</h3>
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                            <div class="text-center">
                                <h4 class="text-sm font-medium text-gray-500">Total Revenue</h4>
                                <p class="mt-2 text-3xl font-bold text-gray-900">{{ formatCurrency(revenue?.total) }}</p>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                            <div class="text-center">
                                <h4 class="text-sm font-medium text-gray-500">Collected</h4>
                                <p class="mt-2 text-3xl font-bold text-green-600">{{ formatCurrency(revenue?.collected) }}</p>
                            </div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                            <div class="text-center">
                                <h4 class="text-sm font-medium text-gray-500">Outstanding</h4>
                                <p class="mt-2 text-3xl font-bold text-red-600">{{ formatCurrency(revenue?.outstanding) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Stats -->
                <div class="mb-8">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Today's Activity</h3>
                    <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5">
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 text-center">
                            <h4 class="text-sm font-medium text-gray-500">New Patients</h4>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ todayStats?.newPatients || 0 }}</p>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 text-center">
                            <h4 class="text-sm font-medium text-gray-500">Completed Appts</h4>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ todayStats?.completedAppointments || 0 }}</p>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 text-center">
                            <h4 class="text-sm font-medium text-gray-500">No-Shows</h4>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ todayStats?.noShows || 0 }}</p>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 text-center">
                            <h4 class="text-sm font-medium text-gray-500">Invoices</h4>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ todayStats?.invoices || 0 }}</p>
                        </div>

                        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 text-center">
                            <h4 class="text-sm font-medium text-gray-500">Today's Revenue</h4>
                            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ formatCurrency(todayStats?.revenue) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tables Section - 2 column layout -->
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Today's Appointments Table -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-900">Today's Appointments</h3>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Time</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Patient</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-if="!todayAppointments || todayAppointments.length === 0">
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No appointments scheduled for today
                                            </td>
                                        </tr>
                                        <tr v-for="appointment in todayAppointments" :key="appointment.id" class="hover:bg-gray-50">
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                {{ formatTime(appointment.start_time) }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ appointment.patient?.first_name }} {{ appointment.patient?.last_name }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                                    :class="{
                                                        'bg-green-100 text-green-800': appointment.status === 'completed',
                                                        'bg-blue-100 text-blue-800': appointment.status === 'scheduled',
                                                        'bg-red-100 text-red-800': appointment.status === 'no-show',
                                                        'bg-gray-100 text-gray-800': appointment.status === 'cancelled'
                                                    }">
                                                    {{ appointment.status }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Invoices Table -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Invoices</h3>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Invoice #</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Patient</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Amount</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-if="!recentInvoices || recentInvoices.length === 0">
                                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No recent invoices
                                            </td>
                                        </tr>
                                        <tr v-for="invoice in recentInvoices" :key="invoice.id" class="hover:bg-gray-50">
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                #{{ invoice.invoice_number || invoice.id }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ invoice.patient?.first_name }} {{ invoice.patient?.last_name }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                {{ formatCurrency(invoice.total_amount) }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                                <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5"
                                                    :class="{
                                                        'bg-green-100 text-green-800': invoice.paid_amount >= invoice.total_amount,
                                                        'bg-yellow-100 text-yellow-800': invoice.paid_amount > 0 && invoice.paid_amount < invoice.total_amount,
                                                        'bg-red-100 text-red-800': invoice.paid_amount === 0
                                                    }">
                                                    {{ invoice.paid_amount >= invoice.total_amount ? 'Paid' : 
                                                       invoice.paid_amount > 0 ? 'Partial' : 'Unpaid' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Provider Performance Table -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-900">Top Providers</h3>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Provider</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Encounters</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-if="!providerPerformance || providerPerformance.length === 0">
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No provider data available
                                            </td>
                                        </tr>
                                        <tr v-for="provider in providerPerformance" :key="provider.id" class="hover:bg-gray-50">
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ provider.name }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                {{ provider.encounters_count || 0 }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                                {{ formatCurrency(provider.revenue) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Appointments Section -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-900">Upcoming Appointments</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div class="overflow-hidden rounded-md bg-blue-50 p-4 shadow">
                                    <h4 class="text-center text-sm font-medium text-blue-800">This Week</h4>
                                    <p class="mt-2 text-center text-2xl font-bold text-blue-900">{{ upcomingAppointments?.thisWeek || 0 }}</p>
                                </div>

                                <div class="overflow-hidden rounded-md bg-green-50 p-4 shadow">
                                    <h4 class="text-center text-sm font-medium text-green-800">This Month</h4>
                                    <p class="mt-2 text-center text-2xl font-bold text-green-900">{{ upcomingAppointments?.thisMonth || 0 }}</p>
                                </div>

                                <div class="overflow-hidden rounded-md bg-purple-50 p-4 shadow">
                                    <h4 class="text-center text-sm font-medium text-purple-800">Next Month</h4>
                                    <p class="mt-2 text-center text-2xl font-bold text-purple-900">{{ upcomingAppointments?.nextMonth || 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
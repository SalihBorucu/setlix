<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSCard, DSButton } from '@/Components/UI'

const props = defineProps({
    bands: {
        type: Array,
        default: () => []
    },
    stats: {
        type: Object,
        default: () => ({
            totalSongs: 0,
            activeSetlists: 0,
            totalDuration: '0h',
            totalMembers: 0
        })
    },
    recentActivity: {
        type: Array,
        default: () => []
    }
})

const stats = [
    { name: 'Total Songs', value: props.stats.totalSongs, change: '+12%', changeType: 'increase' },
    { name: 'Active Setlists', value: props.stats.activeSetlists, change: '+3%', changeType: 'increase' },
    { name: 'Total Duration', value: props.stats.totalDuration, change: '+2.3%', changeType: 'increase' },
    { name: 'Band Members', value: props.stats.totalMembers, change: '0%', changeType: 'neutral' },
]
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Dashboard
                    </h2>
                </div>
            </div>
        </template>

        <!-- No Bands State -->
        <div v-if="bands.length === 0" class="text-center">
            <DSCard class="px-6 py-12">
                <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2M12 3a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-neutral-900">No bands yet</h3>
                <p class="mt-1 text-sm text-neutral-500">Get started by creating your first band.</p>
                <div class="mt-6">
                    <Link :href="route('bands.create')">
                        <DSButton variant="primary" size="lg">
                            Create Your First Band
                        </DSButton>
                    </Link>
                </div>
            </DSCard>
        </div>

        <template v-else>
            <!-- Stats -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <DSCard v-for="item in stats" :key="item.name" class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-neutral-500 truncate">{{ item.name }}</dt>
                    <dd class="mt-1 flex items-baseline justify-between md:block lg:flex">
                        <div class="flex items-baseline text-2xl font-semibold text-neutral-900">
                            {{ item.value }}
                            <span class="ml-2 text-sm font-medium text-neutral-500">from last month</span>
                        </div>

                        <div :class="[
                            item.changeType === 'increase' ? 'bg-success-50 text-success-700' : 'bg-neutral-50 text-neutral-700',
                            'inline-flex items-baseline rounded-full px-2.5 py-0.5 text-sm font-medium md:mt-2 lg:mt-0'
                        ]">
                            <svg v-if="item.changeType === 'increase'" class="-ml-1 mr-0.5 h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                            </svg>
                            {{ item.change }}
                        </div>
                    </dd>
                </DSCard>
            </div>

            <!-- Recent Activity -->
            <DSCard class="mt-6">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-neutral-900">Recent Activity</h3>
                </div>
                <div class="border-t border-neutral-200">
                    <ul v-if="recentActivity.length > 0" role="list" class="divide-y divide-neutral-200">
                        <li v-for="item in recentActivity" :key="item.id" class="px-4 py-4 sm:px-6 hover:bg-neutral-50">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 text-2xl">{{ item.icon }}</div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-neutral-900">
                                        <span class="font-semibold">{{ item.name }}</span>
                                        <span class="text-neutral-500"> {{ item.action }} </span>
                                        <span v-if="item.target" class="font-semibold">{{ item.target }}</span>
                                    </p>
                                    <p class="text-sm text-neutral-500">{{ item.date }}</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div v-else class="px-4 py-8 text-center text-sm text-neutral-500">
                        No recent activity
                    </div>
                </div>
            </DSCard>

            <!-- Your Bands -->
            <DSCard class="mt-6">
                <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-medium leading-6 text-neutral-900">Your Bands</h3>
                    <Link :href="route('bands.create')">
                        <DSButton variant="primary" size="sm">
                            Create New Band
                        </DSButton>
                    </Link>
                </div>
                <div class="border-t border-neutral-200">
                    <ul role="list" class="divide-y divide-neutral-200">
                        <li v-for="band in bands" :key="band.id" class="px-4 py-4 sm:px-6 hover:bg-neutral-50">
                            <div class="flex items-center justify-between">
                                <div class="min-w-0 flex-1">
                                    <Link 
                                        :href="route('bands.show', band.id)"
                                        class="text-sm font-medium text-neutral-900 hover:text-primary-600"
                                    >
                                        {{ band.name }}
                                    </Link>
                                    <p class="text-sm text-neutral-500">{{ band.members_count }} members</p>
                                </div>
                                <div class="ml-4 flex items-center space-x-4">
                                    <Link 
                                        :href="route('songs.index', { band: band.id })"
                                        class="text-sm font-medium text-primary-600 hover:text-primary-700"
                                    >
                                        View Songs
                                    </Link>
                                    <Link 
                                        :href="route('setlists.index', { band: band.id })"
                                        class="text-sm font-medium text-primary-600 hover:text-primary-700"
                                    >
                                        View Setlists
                                    </Link>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </DSCard>
        </template>
    </AuthenticatedLayout>
</template>

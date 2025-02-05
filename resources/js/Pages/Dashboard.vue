<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    bands: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
                <Link
                    :href="route('bands.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                >
                    Create New Band
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <Link
                                :href="route('bands.index')"
                                class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                            >
                                <div>
                                    <h4 class="font-medium">My Bands</h4>
                                    <p class="text-sm text-gray-600">View and manage your bands</p>
                                </div>
                            </Link>
                            <Link
                                :href="route('bands.create')"
                                class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                            >
                                <div>
                                    <h4 class="font-medium">Create Band</h4>
                                    <p class="text-sm text-gray-600">Start a new band</p>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Bands -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Your Bands</h3>
                            <Link
                                :href="route('bands.index')"
                                class="text-sm text-indigo-600 hover:text-indigo-800"
                            >
                                View All →
                            </Link>
                        </div>
                        <div v-if="bands.length === 0" class="text-center py-8">
                            <p class="text-gray-500">You haven't joined any bands yet.</p>
                            <Link
                                :href="route('bands.create')"
                                class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                            >
                                Create Your First Band
                            </Link>
                        </div>
                        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="band in bands"
                                :key="band.id"
                                class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition"
                            >
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-2">
                                        <h3 class="text-xl font-semibold">{{ band.name }}</h3>
                                        <span
                                            v-if="band.pivot.role === 'admin'"
                                            class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full"
                                        >
                                            Admin
                                        </span>
                                    </div>
                                    <p class="text-gray-600 mb-4 line-clamp-2">{{ band.description }}</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-500">
                                                {{ band.members?.length || 0 }} members
                                            </span>
                                            <span class="text-xs text-gray-400">
                                                You are a {{ band.pivot.role }}
                                            </span>
                                        </div>
                                        <Link
                                            :href="route('bands.show', band.id)"
                                            class="text-sm text-indigo-600 hover:text-indigo-800"
                                        >
                                            View Details →
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

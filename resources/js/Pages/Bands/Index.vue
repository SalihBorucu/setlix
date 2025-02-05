<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    bands: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <Head title="My Bands" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    My Bands
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
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
                                    <h3 class="text-xl font-semibold mb-2">{{ band.name }}</h3>
                                    <p class="text-gray-600 mb-4 line-clamp-2">{{ band.description }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">
                                            {{ band.members.length }} members
                                        </span>
                                        <Link
                                            :href="route('bands.show', band.id)"
                                            class="text-indigo-600 hover:text-indigo-800"
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
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    }
});

const confirmingDeletion = ref(false);

const deleteBand = () => {
    if (confirm('Are you sure you want to delete this band? This action cannot be undone.')) {
        router.delete(route('bands.destroy', props.band.id));
    }
};
</script>

<template>
    <Head :title="band.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ band.name }}
                </h2>
                <div v-if="isAdmin" class="flex gap-4">
                    <Link
                        :href="route('bands.edit', band.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                    >
                        Edit Band
                    </Link>
                    <button
                        @click="deleteBand"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                    >
                        Delete Band
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Band Description -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-2">About</h3>
                            <p class="text-gray-600">{{ band.description || 'No description provided.' }}</p>
                        </div>

                        <!-- Songs Management -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Songs</h3>
                                <div class="flex gap-4">
                                    <Link
                                        :href="route('songs.index', band.id)"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                                    >
                                        View All Songs
                                    </Link>
                                    <Link
                                        v-if="isAdmin"
                                        :href="route('songs.create', band.id)"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                                    >
                                        Add New Song
                                    </Link>
                                </div>
                            </div>
                            <p class="text-gray-600">Manage your band's song library and organize your music.</p>
                        </div>

                        <!-- Setlists Management -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Setlists</h3>
                                <div class="flex gap-4">
                                    <Link
                                        :href="route('setlists.index', band.id)"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                                    >
                                        View All Setlists
                                    </Link>
                                    <Link
                                        v-if="isAdmin"
                                        :href="route('setlists.create', band.id)"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition"
                                    >
                                        Create New Setlist
                                    </Link>
                                </div>
                            </div>
                            <p class="text-gray-600">Create and manage setlists for your performances, with drag-and-drop song arrangement and performance mode.</p>
                        </div>

                        <!-- Members List -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Members</h3>
                            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                                <div
                                    v-for="member in band.members"
                                    :key="member.id"
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                                >
                                    <div>
                                        <p class="font-medium">{{ member.name }}</p>
                                        <p class="text-sm text-gray-500 capitalize">{{ member.pivot.role }}</p>
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
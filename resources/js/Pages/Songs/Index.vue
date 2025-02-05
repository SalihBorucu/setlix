<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    songs: {
        type: Array,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    }
});

const deleteSong = (songId) => {
    if (confirm('Are you sure you want to delete this song? This action cannot be undone.')) {
        router.delete(route('songs.destroy', [props.band.id, songId]));
    }
};
</script>

<template>
    <Head :title="`${band.name} - Songs`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ band.name }} - Songs
                    </h2>
                    <p class="text-sm text-gray-600">Manage your band's song library</p>
                </div>
                <div class="flex gap-4">
                    <Link
                        :href="route('bands.show', band.id)"
                        class="px-4 py-2 text-gray-700 rounded-md border hover:bg-gray-50 transition"
                    >
                        Back to Band
                    </Link>
                    <Link
                        v-if="isAdmin"
                        :href="route('songs.create', band.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                    >
                        Add New Song
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="songs.length === 0" class="text-center py-8">
                            <p class="text-gray-500">No songs have been added yet.</p>
                            <Link
                                v-if="isAdmin"
                                :href="route('songs.create', band.id)"
                                class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                            >
                                Add Your First Song
                            </Link>
                        </div>
                        <div v-else>
                            <!-- Songs List -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Duration
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Resources
                                            </th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="song in songs" :key="song.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <Link
                                                    :href="route('songs.show', [band.id, song.id])"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    {{ song.name }}
                                                </Link>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ song.formatted_duration }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center space-x-2">
                                                    <a
                                                        v-if="song.url"
                                                        :href="song.url"
                                                        target="_blank"
                                                        class="text-gray-600 hover:text-gray-900"
                                                    >
                                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">URL</span>
                                                    </a>
                                                    <Link
                                                        v-if="song.document_path"
                                                        :href="route('songs.document', [band.id, song.id])"
                                                        class="text-gray-600 hover:text-gray-900"
                                                    >
                                                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">
                                                            {{ song.document_type?.toUpperCase() }}
                                                        </span>
                                                    </Link>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <Link
                                                        :href="route('songs.show', [band.id, song.id])"
                                                        class="text-indigo-600 hover:text-indigo-900"
                                                    >
                                                        View
                                                    </Link>
                                                    <Link
                                                        v-if="isAdmin"
                                                        :href="route('songs.edit', [band.id, song.id])"
                                                        class="text-gray-600 hover:text-gray-900"
                                                    >
                                                        Edit
                                                    </Link>
                                                    <button
                                                        v-if="isAdmin"
                                                        @click="deleteSong(song.id)"
                                                        class="text-red-600 hover:text-red-900"
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 
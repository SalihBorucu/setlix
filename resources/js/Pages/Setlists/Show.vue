<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    setlist: {
        type: Object,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    }
});

const deleteModal = ref(null);
const performanceMode = ref(false);

const confirmDelete = () => {
    deleteModal.value?.open();
};

const handleDelete = () => {
    router.delete(route('setlists.destroy', [props.band.id, props.setlist.id]));
};

const formatDuration = (seconds) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const remainingSeconds = seconds % 60;

    if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

// Sort songs by their order in the setlist
const sortedSongs = computed(() => {
    return [...props.setlist.songs].sort((a, b) => a.pivot.order - b.pivot.order);
});
</script>

<template>
    <Head :title="`${setlist.name} - ${band.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ setlist.name }}
                    </h2>
                    <p class="text-sm text-gray-600">{{ band.name }}</p>
                </div>
                <div class="flex gap-4">
                    <button
                        @click="performanceMode = !performanceMode"
                        class="px-4 py-2 text-gray-700 rounded-md border hover:bg-gray-50 transition"
                        :class="{ 'bg-gray-100': performanceMode }"
                    >
                        {{ performanceMode ? 'Exit Performance Mode' : 'Performance Mode' }}
                    </button>
                    <template v-if="isAdmin">
                        <Link
                            :href="route('setlists.edit', [band.id, setlist.id])"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                        >
                            Edit Setlist
                        </Link>
                        <button
                            @click="confirmDelete"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                        >
                            Delete Setlist
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Performance Mode -->
                <div v-if="performanceMode" class="mb-6">
                    <div class="bg-black text-white p-8 rounded-lg shadow-xl">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-3xl font-bold">{{ setlist.name }}</h2>
                            <div class="text-xl">
                                Total Duration: {{ formatDuration(setlist.total_duration) }}
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div
                                v-for="(song, index) in sortedSongs"
                                :key="song.id"
                                class="p-6 border border-gray-700 rounded-lg"
                            >
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="text-2xl font-semibold">{{ index + 1 }}. {{ song.name }}</span>
                                        <div class="text-gray-400 mt-2">{{ formatDuration(song.duration_seconds) }}</div>
                                    </div>
                                    <div class="flex gap-4">
                                        <a
                                            v-if="song.url"
                                            :href="song.url"
                                            target="_blank"
                                            class="text-indigo-400 hover:text-indigo-300"
                                        >
                                            External Link
                                        </a>
                                        <Link
                                            v-if="song.document_path"
                                            :href="route('songs.document', [band.id, song.id])"
                                            class="text-indigo-400 hover:text-indigo-300"
                                        >
                                            View Document
                                        </Link>
                                    </div>
                                </div>
                                <div v-if="song.pivot.notes" class="mt-4 text-gray-400">
                                    {{ song.pivot.notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Normal Mode -->
                <div v-else>
                    <!-- Setlist Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-semibold mb-2">About this Setlist</h3>
                                    <p class="text-gray-600">{{ setlist.description || 'No description provided.' }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500">Total Duration</div>
                                    <div class="text-xl font-semibold">
                                        {{ formatDuration(setlist.total_duration) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Songs List -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Songs</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Order
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Duration
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Resources
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Notes
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="(song, index) in sortedSongs" :key="song.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                {{ index + 1 }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <Link
                                                    :href="route('songs.show', [band.id, song.id])"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    {{ song.name }}
                                                </Link>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatDuration(song.duration_seconds) }}
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
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ song.pivot.notes || '-' }}
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

        <DeleteConfirmationModal
            ref="deleteModal"
            :title="`Delete ${setlist.name}`"
            :message="'Are you sure you want to delete this setlist? This action cannot be undone.'"
            @confirm="handleDelete"
        />
    </AuthenticatedLayout>
</template> 
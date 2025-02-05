<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    song: {
        type: Object,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    }
});

const deleteModal = ref(null);

const confirmDelete = () => {
    router.delete(route('songs.destroy', [props.band.id, props.song.id]));
};
</script>

<template>
    <Head :title="`${song.name} - ${band.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ song.name }}
                    </h2>
                    <p class="text-sm text-gray-600">{{ band.name }}</p>
                </div>
                <div v-if="isAdmin" class="flex gap-4">
                    <Link
                        :href="route('songs.edit', [band.id, song.id])"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                    >
                        Edit Song
                    </Link>
                    <button
                        @click="deleteModal.open()"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
                    >
                        Delete Song
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Song Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Details</h3>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                        <dd class="mt-1 text-lg text-gray-900">{{ song.formatted_duration }}</dd>
                                    </div>
                                    <div v-if="song.url">
                                        <dt class="text-sm font-medium text-gray-500">External Link</dt>
                                        <dd class="mt-1">
                                            <a
                                                :href="song.url"
                                                target="_blank"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                {{ song.url }}
                                            </a>
                                        </dd>
                                    </div>
                                    <div v-if="song.document_path">
                                        <dt class="text-sm font-medium text-gray-500">Document</dt>
                                        <dd class="mt-1">
                                            <a
                                                :href="route('songs.document', [band.id, song.id])"
                                                class="inline-flex items-center text-indigo-600 hover:text-indigo-900"
                                                download
                                            >
                                                <span>Download {{ song.document_type?.toUpperCase() }}</span>
                                            </a>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            <div v-if="song.notes">
                                <h3 class="text-lg font-semibold mb-4">Notes</h3>
                                <div class="prose max-w-none">
                                    <p class="text-gray-600 whitespace-pre-line">{{ song.notes }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-8 flex justify-end">
                            <Link
                                :href="route('songs.index', band.id)"
                                class="text-gray-600 hover:text-gray-900"
                            >
                                Back to Songs
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <DeleteConfirmationModal
            ref="deleteModal"
            :title="`Delete ${song.name}`"
            :message="'Are you sure you want to delete this song? This action cannot be undone.'"
            @confirm="confirmDelete"
        />
    </AuthenticatedLayout>
</template> 
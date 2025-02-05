<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal.vue';
import { ref } from 'vue';

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    setlists: {
        type: Array,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    }
});

const deleteModal = ref(null);
const setlistToDelete = ref(null);

const confirmDelete = (setlist) => {
    setlistToDelete.value = setlist;
    deleteModal.value?.open();
};

const handleDelete = () => {
    if (setlistToDelete.value) {
        router.delete(route('setlists.destroy', [props.band.id, setlistToDelete.value.id]));
    }
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
</script>

<template>
    <Head :title="`${band.name} - Setlists`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ band.name }} - Setlists
                    </h2>
                    <p class="text-sm text-gray-600">Manage your band's setlists</p>
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
                        :href="route('setlists.create', band.id)"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                    >
                        Create New Setlist
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="setlists.length === 0" class="text-center py-8">
                            <p class="text-gray-500">No setlists have been created yet.</p>
                            <Link
                                v-if="isAdmin"
                                :href="route('setlists.create', band.id)"
                                class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                            >
                                Create Your First Setlist
                            </Link>
                        </div>
                        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                            <div
                                v-for="setlist in setlists"
                                :key="setlist.id"
                                class="bg-white border rounded-lg overflow-hidden hover:shadow-lg transition"
                            >
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2">{{ setlist.name }}</h3>
                                    <p v-if="setlist.description" class="text-gray-600 mb-4 line-clamp-2">
                                        {{ setlist.description }}
                                    </p>
                                    <div class="flex flex-col gap-2">
                                        <div class="flex justify-between text-sm text-gray-500">
                                            <span>{{ setlist.songs?.length || 0 }} songs</span>
                                            <span>{{ formatDuration(setlist.total_duration) }}</span>
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <Link
                                                :href="route('setlists.show', [band.id, setlist.id])"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                View
                                            </Link>
                                            <Link
                                                v-if="isAdmin"
                                                :href="route('setlists.edit', [band.id, setlist.id])"
                                                class="text-gray-600 hover:text-gray-900"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                v-if="isAdmin"
                                                @click="confirmDelete(setlist)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <DeleteConfirmationModal
            ref="deleteModal"
            :title="`Delete ${setlistToDelete?.name || 'Setlist'}`"
            :message="'Are you sure you want to delete this setlist? This action cannot be undone.'"
            @confirm="handleDelete"
        />
    </AuthenticatedLayout>
</template> 
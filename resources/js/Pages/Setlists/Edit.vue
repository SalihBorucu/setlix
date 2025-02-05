<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    setlist: {
        type: Object,
        required: true
    },
    availableSongs: {
        type: Array,
        required: true
    }
});

const form = useForm({
    band_id: props.band.id,
    name: props.setlist.name,
    description: props.setlist.description,
    song_order: [],
    total_duration: props.setlist.total_duration
});

// Initialize available and selected songs
const selectedSongs = ref(props.setlist.songs.sort((a, b) => a.pivot.order - b.pivot.order));
const availableSongs = ref(
    props.availableSongs.filter(song => 
        !selectedSongs.value.some(selected => selected.id === song.id)
    )
);

// Update total duration whenever songs are reordered
const updateTotalDuration = () => {
    form.total_duration = selectedSongs.value.reduce((total, song) => total + song.duration_seconds, 0);
};

// Format duration for display
const formatDuration = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

// Computed total duration formatted
const totalDurationFormatted = computed(() => formatDuration(form.total_duration));

const submit = () => {
    form.song_order = selectedSongs.value.map(song => song.id);
    form.patch(route('setlists.update', [props.band.id, props.setlist.id]), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head :title="`Edit ${setlist.name} - ${band.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Setlist - {{ band.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Basic Information -->
                            <div class="mb-6">
                                <InputLabel for="name" value="Setlist Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                    autofocus
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div class="mb-6">
                                <InputLabel for="description" value="Description (Optional)" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                ></textarea>
                                <InputError :message="form.errors.description" class="mt-2" />
                            </div>

                            <!-- Song Selection -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Available Songs -->
                                <div>
                                    <h3 class="text-lg font-medium mb-4">Available Songs</h3>
                                    <draggable
                                        v-model="availableSongs"
                                        :group="{ name: 'songs', pull: 'clone', put: true }"
                                        item-key="id"
                                        class="min-h-[200px] border-2 border-dashed border-gray-200 rounded-lg p-4"
                                        @change="updateTotalDuration"
                                    >
                                        <template #item="{ element }">
                                            <div class="flex justify-between items-center p-3 mb-2 bg-gray-50 rounded-lg cursor-move hover:bg-gray-100">
                                                <span>{{ element.name }}</span>
                                                <span class="text-sm text-gray-500">{{ formatDuration(element.duration_seconds) }}</span>
                                            </div>
                                        </template>
                                    </draggable>
                                </div>

                                <!-- Selected Songs -->
                                <div>
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-medium">Setlist</h3>
                                        <span class="text-sm text-gray-500">
                                            Total Duration: {{ totalDurationFormatted }}
                                        </span>
                                    </div>
                                    <draggable
                                        v-model="selectedSongs"
                                        :group="{ name: 'songs' }"
                                        item-key="id"
                                        class="min-h-[200px] border-2 border-dashed border-gray-200 rounded-lg p-4"
                                        @change="updateTotalDuration"
                                    >
                                        <template #item="{ element }">
                                            <div class="flex justify-between items-center p-3 mb-2 bg-white border rounded-lg cursor-move hover:bg-gray-50">
                                                <div>
                                                    <span>{{ element.name }}</span>
                                                    <input
                                                        type="text"
                                                        v-model="element.pivot.notes"
                                                        placeholder="Add notes..."
                                                        class="mt-2 block w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                    />
                                                </div>
                                                <span class="text-sm text-gray-500">{{ formatDuration(element.duration_seconds) }}</span>
                                            </div>
                                        </template>
                                        <template #footer>
                                            <div v-if="selectedSongs.length === 0" class="text-center text-gray-500 py-4">
                                                Drag songs here to add them to the setlist
                                            </div>
                                        </template>
                                    </draggable>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-4">
                                <Link
                                    :href="route('setlists.show', [band.id, setlist.id])"
                                    class="px-4 py-2 text-gray-700 rounded-md border hover:bg-gray-50 transition"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing || selectedSongs.length === 0"
                                >
                                    Update Setlist
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 
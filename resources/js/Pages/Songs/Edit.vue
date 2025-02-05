<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    song: {
        type: Object,
        required: true
    }
});

const form = useForm({
    band_id: props.band.id,
    name: props.song.name,
    duration_seconds: props.song.duration_seconds,
    notes: props.song.notes,
    url: props.song.url,
    document: null
});

const formatDuration = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const parseDuration = (duration) => {
    const [minutes, seconds] = duration.split(':').map(Number);
    return minutes * 60 + seconds;
};

const durationInput = ref(formatDuration(props.song.duration_seconds));

const updateDuration = () => {
    const duration = durationInput.value;
    if (/^\d+:\d{2}$/.test(duration)) {
        form.duration_seconds = parseDuration(duration);
    }
};

const submit = () => {
    form.post(route('songs.update', [props.band.id, props.song.id]), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        }
    });
};
</script>

<template>
    <Head :title="`Edit ${song.name} - ${band.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Edit Song
                    </h2>
                    <p class="text-sm text-gray-600">{{ band.name }}</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Song Name -->
                            <div>
                                <InputLabel for="name" value="Song Name" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <!-- Duration -->
                            <div>
                                <InputLabel for="duration" value="Duration (MM:SS)" />
                                <TextInput
                                    id="duration"
                                    v-model="durationInput"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="3:30"
                                    pattern="\d+:\d{2}"
                                    @input="updateDuration"
                                    required
                                />
                                <InputError :message="form.errors.duration_seconds" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div>
                                <InputLabel for="notes" value="Notes (Optional)" />
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="4"
                                ></textarea>
                                <InputError :message="form.errors.notes" class="mt-2" />
                            </div>

                            <!-- URL -->
                            <div>
                                <InputLabel for="url" value="URL (Optional)" />
                                <TextInput
                                    id="url"
                                    v-model="form.url"
                                    type="url"
                                    class="mt-1 block w-full"
                                />
                                <InputError :message="form.errors.url" class="mt-2" />
                            </div>

                            <!-- Document -->
                            <div>
                                <InputLabel for="document" value="Document (Optional)" />
                                <input
                                    id="document"
                                    type="file"
                                    @input="form.document = $event.target.files[0]"
                                    class="mt-1 block w-full"
                                    accept=".pdf,.txt"
                                />
                                <p v-if="song.document_path" class="mt-2 text-sm text-gray-600">
                                    Current document: {{ song.document_type?.toUpperCase() }}
                                </p>
                                <InputError :message="form.errors.document" class="mt-2" />
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end gap-4">
                                <Link
                                    :href="route('songs.show', [band.id, song.id])"
                                    class="text-gray-600 hover:text-gray-900"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                                    :disabled="form.processing"
                                >
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import {DSInput} from "@/Components/UI/index.js";

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
    artist: props.song.artist,
    bpm: props.song.bpm,
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
    form.patch(route('songs.update', [props.band.id, props.song.id]), {
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
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Song Name -->
                                <div>
                                    <DSInput
                                        v-model="form.name"
                                        type="text"
                                        label="Song Name"
                                        :error="form.errors.name"
                                        required
                                    />
                                </div>

                                <div>
                                    <DSInput
                                        v-model="form.artist"
                                        type="text"
                                        label="Artist (Optional)"
                                        :error="form.errors.artist"
                                        placeholder=""
                                    />
                                </div>

                                <div>
                                    <DSInput
                                        v-model="durationInput"
                                        type="text"
                                        label="Duration (MM:SS)"
                                        :error="form.errors.duration_seconds"
                                        required
                                        pattern="[0-9]{1,2}:[0-9]{2}"
                                        placeholder="03:30"
                                    />
                                </div>

                                <div>
                                    <DSInput
                                        v-model="form.bpm"
                                        type="number"
                                        label="BPM (Optional)"
                                        :error="form.errors.bpm"
                                    />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-neutral-700">
                                    Notes (Optional)
                                </label>
                                <div class="mt-1">
                                    <textarea
                                        v-model="form.notes"
                                        rows="4"
                                        :class="[
                                'block w-full rounded-lg shadow-sm transition-colors duration-200',
                                'border-neutral-300 focus:border-primary-500 focus:ring-primary-500',
                                { 'border-error-500 focus:border-error-500 focus:ring-error-500': form.errors.notes }
                            ]"
                                    />
                                </div>
                                <p class="mt-1 text-sm text-neutral-500">
                                    Add any notes about the song, such as key changes or special instructions
                                </p>
                            </div>

                            <div>
                                <DSInput
                                    v-model="form.url"
                                    type="url"
                                    label="External Link (Optional)"
                                    :error="form.errors.url"
                                    placeholder="https://"
                                />
                                <p class="mt-1 text-sm text-neutral-500">
                                    Link to an external resource (YouTube, Spotify, etc.)
                                </p>
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

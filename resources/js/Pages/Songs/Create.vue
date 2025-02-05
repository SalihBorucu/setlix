<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const props = defineProps({
    band: {
        type: Object,
        required: true
    }
});

const form = useForm({
    band_id: props.band.id,
    name: '',
    duration_seconds: '',
    notes: '',
    url: '',
    document: null
});

// Convert MM:SS to seconds
const formatDuration = (value) => {
    const [minutes, seconds] = value.split(':').map(Number);
    return minutes * 60 + seconds;
};

// Convert seconds to MM:SS
const parseDuration = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const durationInput = ref('');

const submit = () => {
    // Convert duration from MM:SS to seconds before submitting
    form.duration_seconds = formatDuration(durationInput.value);
    form.post(route('songs.store', props.band.id));
};

const handleFileChange = (event) => {
    form.document = event.target.files[0];
};
</script>

<template>
    <Head title="Add New Song" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Add New Song to {{ band.name }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Song Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="duration" value="Duration (MM:SS)" />
                                <TextInput
                                    id="duration"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="durationInput"
                                    required
                                    pattern="[0-9]{1,2}:[0-9]{2}"
                                    placeholder="03:30"
                                />
                                <InputError class="mt-2" :message="form.errors.duration_seconds" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="notes" value="Notes (Optional)" />
                                <textarea
                                    id="notes"
                                    v-model="form.notes"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.notes" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="url" value="URL (Optional)" />
                                <TextInput
                                    id="url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    v-model="form.url"
                                    placeholder="https://"
                                />
                                <InputError class="mt-2" :message="form.errors.url" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="document" value="Document (Optional)" />
                                <input
                                    type="file"
                                    id="document"
                                    @change="handleFileChange"
                                    class="mt-1 block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100"
                                    accept=".pdf,.txt"
                                />
                                <p class="mt-1 text-sm text-gray-500">
                                    Accepted formats: PDF, TXT (max. 10MB)
                                </p>
                                <InputError class="mt-2" :message="form.errors.document" />
                            </div>

                            <div class="mt-6 flex items-center justify-end gap-4">
                                <Link
                                    :href="route('songs.index', band.id)"
                                    class="text-gray-600 hover:text-gray-900"
                                >
                                    Cancel
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Create Song
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 
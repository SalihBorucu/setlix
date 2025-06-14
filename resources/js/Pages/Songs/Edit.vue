<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, router, useForm} from '@inertiajs/vue3';
import { ref } from 'vue';
import {DSInput, DSButton, DSCard, DSAlert, DSAlertModal, DSDurationInput} from "@/Components/UI";
import AddFileSection from "@/Pages/Songs/AddFileSection.vue";

// Move helper functions before they're used
const formatDuration = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const parseDuration = (duration) => {
    const [minutes, seconds] = duration.split(':').map(Number);
    return minutes * 60 + seconds;
};

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
    duration: formatDuration(props.song.duration_seconds),
    duration_seconds: props.song.duration_seconds,
    notes: props.song.notes,
    url: props.song.url,
    files: []
});

const fileGroups = ref([]);
const deleteModal = ref(null);
const fileToDelete = ref(null);

const addFileGroup = () => {
    if (fileGroups.value.length < 10) {
        fileGroups.value.push({
            type: '',
            instrument: null,
            file: null
        });
    }
};

const removeFileGroup = (index) => {
    fileGroups.value.splice(index, 1);
};

const handleFileUpload = (event, index) => {
    const file = event.target.files[0];
    if (file) {
        fileGroups.value[index].file = file;
    }
};

const confirmDeleteFile = (file) => {
    fileToDelete.value = file;
    deleteModal.value.open();
};

const deleteFile = () => {
    router.delete(route('songs.files.destroy', [props.band.id, props.song.id, fileToDelete.value.id]), {
        preserveScroll: true,
        onSuccess: () => {
            deleteModal.value.close();
            fileToDelete.value = null;
        }
    });
};

const submit = () => {
    const formData = new FormData();

    formData.append('_method', 'PATCH');
    formData.append('band_id', form.band_id.toString());
    formData.append('name', form.name);
    formData.append('duration_seconds', form.duration_seconds.toString());

    if (form.artist) formData.append('artist', form.artist);
    if (form.duration) formData.append('duration', form.duration);
    if (form.notes) formData.append('notes', form.notes);
    if (form.url) formData.append('url', form.url);

    fileGroups.value
        .filter(group => group.file && group.type)
        .forEach((group, index) => {
            formData.append(`files[${index}][type]`, group.type);
            formData.append(`files[${index}][instrument]`, group.instrument);
            formData.append(`files[${index}][file]`, group.file);
        });

    router.post(route('songs.update', [props.band.id, props.song.id]), formData, {
        preserveScroll: true,
        forceFormData: true
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
                                    <DSDurationInput
                                        v-model="form.duration"
                                        @update:seconds="(seconds) => form.duration_seconds = seconds"
                                        label="Duration"
                                        required
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

                            <!-- Existing Files -->
                            <div v-if="song.files?.length" class="space-y-4">
                                <h3 class="text-lg font-medium text-neutral-900">Existing Files</h3>
                                <div class="space-y-2">
                                    <div
                                        v-for="file in song.files"
                                        :key="file.id"
                                        class="flex items-center gap-4 border rounded-md p-3"
                                    >
                                        <div class="flex-1">
                                            <span class="text-sm font-medium text-neutral-900">{{ file.type }}</span>
                                            <p class="text-sm text-neutral-500">{{ file.original_filename }}</p>
                                        </div>

                                        <div class="flex items-center space-x-2">
                                            <Link
                                                :href="route('songs.files.download', [band.id, song.id, file.id])"
                                                class="text-primary-600 hover:text-primary-700"
                                            >
                                                Download
                                            </Link>
                                            <button
                                                type="button"
                                                @click="confirmDeleteFile(file)"
                                                class="text-neutral-400 hover:text-neutral-500"
                                            >
                                                <span class="sr-only">Delete</span>
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <AddFileSection :fileGroups="fileGroups"
                                            @add-file-group="addFileGroup"
                                            @remove-file-group="removeFileGroup"
                                            @handle-file-upload="handleFileUpload"
                            />
                            <!-- New Files -->
<!--                            <div class="space-y-4">-->
<!--                                <div class="flex justify-between items-center">-->
<!--                                    <h3 class="text-lg font-medium text-neutral-900">Add New Files</h3>-->
<!--                                    <DSButton-->
<!--                                        type="button"-->
<!--                                        variant="secondary"-->
<!--                                        @click="addFileGroup"-->
<!--                                        :disabled="fileGroups.length >= 10"-->
<!--                                    >-->
<!--                                        Add File-->
<!--                                    </DSButton>-->
<!--                                </div>-->

<!--                                <div class="space-y-2">-->
<!--                                    <div-->
<!--                                        v-for="(group, index) in fileGroups"-->
<!--                                        :key="index"-->
<!--                                        class="flex items-center gap-4 border rounded-md p-3"-->
<!--                                    >-->
<!--                                        <div class="flex-1">-->
<!--                                            <select-->
<!--                                                v-model="group.type"-->
<!--                                                class="block w-full rounded-md border-neutral-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"-->
<!--                                            >-->
<!--                                                <option value="">Select Type</option>-->
<!--                                                <option value="lyrics">Lyrics</option>-->
<!--                                                <option value="notes">Notes</option>-->
<!--                                                <option value="chords">Chords</option>-->
<!--                                                <option value="tabs">Tabs</option>-->
<!--                                                <option value="sheet_music">Sheet Music</option>-->
<!--                                                <option value="other">Other</option>-->
<!--                                            </select>-->
<!--                                        </div>-->

<!--                                        <div class="flex-1">-->
<!--                                            <input-->
<!--                                                type="file"-->
<!--                                                @change="handleFileUpload($event, index)"-->
<!--                                                accept=".pdf,.txt"-->
<!--                                                class="block w-full text-sm"-->
<!--                                            >-->
<!--                                        </div>-->

<!--                                        <button-->
<!--                                            type="button"-->
<!--                                            @click="removeFileGroup(index)"-->
<!--                                            class="text-neutral-400 hover:text-neutral-500"-->
<!--                                        >-->
<!--                                            <span class="sr-only">Remove</span>-->
<!--                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
<!--                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />-->
<!--                                            </svg>-->
<!--                                        </button>-->
<!--                                    </div>-->
<!--                                </div>-->

<!--                                <p class="text-sm text-neutral-500">PDF or TXT files up to 10MB</p>-->
<!--                            </div>-->

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end gap-4">
                                <Link :href="route('songs.show', [band.id, song.id])">
                                    <DSButton
                                        type="button"
                                        variant="outline"
                                    >
                                        Cancel
                                    </DSButton>
                                </Link>
                                <DSButton
                                    type="submit"
                                    variant="primary"
                                    :disabled="form.processing"
                                >
                                    <span v-if="form.processing">Saving...</span>
                                    <span v-else>Save Changes</span>
                                </DSButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <DSAlertModal
        ref="deleteModal"
        title="Delete File"
        message="Are you sure you want to delete this file? This action cannot be undone."
        type="error"
        confirm-text="Delete"
        :show-cancel="true"
        @confirm="deleteFile"
    />
</template>

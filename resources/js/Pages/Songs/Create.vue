<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSInput, DSCard, DSAlert } from '@/Components/UI'
import {computed, ref} from 'vue'
import { DSDurationInput } from '@/Components/UI'

const props = defineProps({
    band: {
        type: Object,
        required: true
    }
})

const form = useForm({
    band_id: props.band.id,
    name: null,
    duration: null,
    duration_seconds: null,
    notes: null,
    url: null,
    bpm: null,
    artist: null,
    files: [],
    spotify_link: null
})

const fileGroups = ref([])

const addFileGroup = () => {
    if (fileGroups.value.length < 10) {
        fileGroups.value.push({
            type: '',
            file: null
        })
    }
}

const removeFileGroup = (index) => {
    fileGroups.value.splice(index, 1)
}

const handleFileUpload = (event, index) => {
    const file = event.target.files[0]
    if (file) {
        fileGroups.value[index].file = file
    }
}

const submit = () => {
    // Add files to form data
    form.files = fileGroups.value
        .filter(group => group.file && group.type) // Only include complete groups
        .map(group => ({
            type: group.type,
            file: group.file
        }))

    form.post(route('songs.store', props.band.id))
}

const anyDataHasChanged = computed(() => {
    return !!form.name || !!form.duration || !!form.notes || !!form.url || !!form.artist || !!form.bpm
});
</script>

<template>
    <Head title="Add New Song" />

    <AuthenticatedLayout class="space-y-2">
        <template #header>
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center">
                        <Link
                            :href="route('bands.show', band.id)"
                            class="text-sm font-medium text-primary-600 hover:text-primary-700"
                        >
                            {{ band.name }}
                        </Link>
                        <svg class="mx-2 h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <Link
                            :href="route('songs.index', band.id)"
                            class="text-sm font-medium text-primary-600 hover:text-primary-700"
                        >
                            Songs
                        </Link>
                    </div>
                    <h2 class="mt-1 text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Add New Song
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        Add a new song to your band's library
                    </p>
                </div>
            </div>
        </template>

        <DSCard class="max-w-2xl space-y-6 p-6 mb-4" :disabled="anyDataHasChanged">
            <p class="text-sm text-gray-600">Enter a Spotify track URL to import its details.</p>
            <DSInput
                v-model="form.spotify_link"
                type="url"
                placeholder="https://open.spotify.com/track/abc123"
                label="Spotify Link"
                :error="form.errors.spotify_link"
                required
            />

            <div class="flex justify-end space-x-3">
                <DSButton
                    @click="submit"
                    variant="primary"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Importing...</span>
                    <span v-else>Import Song</span>
                </DSButton>
            </div>
        </DSCard>

        <DSCard class="max-w-2xl" :disabled="!!form.spotify_link">
            <form @submit.prevent="submit" class="space-y-6 p-6">
                <p class="text-sm text-gray-600">Or manually add a song's for your list.</p>
                <!-- Error Message -->
                <DSAlert
                    v-if="Object.keys(form.errors).length > 0"
                    type="error"
                >
                    <ul class="list-disc list-inside">
                        <li v-for="error in form.errors" :key="error">{{ error }}</li>
                    </ul>
                </DSAlert>

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
                            :error="form.errors.duration_seconds"
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

                <!-- Notes -->
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

                <!-- URL -->
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

                <!-- File Upload Section -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-neutral-900">Files</h3>
                        <DSButton
                            type="button"
                            variant="secondary"
                            @click="addFileGroup"
                            :disabled="fileGroups.length >= 10"
                        >
                            Add File
                        </DSButton>
                    </div>

                    <div class="space-y-2">
                        <div
                            v-for="(group, index) in fileGroups"
                            :key="index"
                            class="flex items-center gap-4 border rounded-md p-3"
                        >
                            <div class="flex-1">
                                <select
                                    v-model="group.type"
                                    class="block w-full rounded-md border-neutral-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                >
                                    <option value="">Select Type</option>
                                    <option value="lyrics">Lyrics</option>
                                    <option value="notes">Notes</option>
                                    <option value="chords">Chords</option>
                                    <option value="tabs">Tabs</option>
                                    <option value="sheet_music">Sheet Music</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="flex-1">
                                <input
                                    type="file"
                                    @change="handleFileUpload($event, index)"
                                    accept=".pdf,.txt"
                                    class="block w-full text-sm"
                                >
                            </div>

                            <button
                                type="button"
                                @click="removeFileGroup(index)"
                                class="text-neutral-400 hover:text-neutral-500"
                            >
                                <span class="sr-only">Remove</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <p class="text-sm text-neutral-500">PDF or TXT files up to 10MB</p>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <Link :href="route('songs.index', band.id)">
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
                        <span v-if="form.processing">Creating...</span>
                        <span v-else>Create Song</span>
                    </DSButton>
                </div>
            </form>
        </DSCard>
    </AuthenticatedLayout>
</template>

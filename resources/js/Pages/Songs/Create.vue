<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSInput, DSCard, DSAlert } from '@/Components/UI'
import { ref } from 'vue'

const props = defineProps({
    band: {
        type: Object,
        required: true
    }
})

const form = useForm({
    band_id: props.band.id,
    name: '',
    duration_seconds: '',
    notes: '',
    url: '',
    bpm: '',
    artist: '',
    document: null
})

// Convert MM:SS to seconds
const formatDuration = (value) => {
    const [minutes, seconds] = value.split(':').map(Number)
    return minutes * 60 + seconds
}

const durationInput = ref('')

const submit = () => {
    // Convert duration from MM:SS to seconds before submitting
    form.duration_seconds = formatDuration(durationInput.value)
    form.post(route('songs.store', props.band.id))
}

const handleFileChange = (event) => {
    form.document = event.target.files[0]
}
</script>

<template>
    <Head title="Add New Song" />

    <AuthenticatedLayout>
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

        <DSCard class="max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6 p-6">
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

                <!-- Document Upload -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700">
                        Document (Optional)
                    </label>
                    <div class="mt-1 flex justify-center rounded-lg border border-dashed border-neutral-300 px-6 py-10">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <div class="mt-4 flex text-sm text-neutral-600">
                                <label
                                    for="document"
                                    class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500"
                                >
                                    <span>Upload a file</span>
                                    <input
                                        id="document"
                                        type="file"
                                        class="sr-only"
                                        accept=".pdf,.txt"
                                        @change="handleFileChange"
                                    >
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-neutral-500">PDF or TXT up to 10MB</p>
                        </div>
                    </div>
                    <p v-if="form.errors.document" class="mt-1 text-sm text-error-500">
                        {{ form.errors.document }}
                    </p>
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

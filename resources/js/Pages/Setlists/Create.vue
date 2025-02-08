<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSInput, DSCard, DSAlert } from '@/Components/UI'
import { ref, computed } from 'vue'
import draggable from 'vuedraggable'

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    songs: {
        type: Array,
        required: true
    }
})

const form = useForm({
    band_id: props.band.id,
    name: '',
    description: '',
    song_order: [],
    total_duration: 0
})

// Available songs (not in setlist)
const availableSongs = ref([...props.songs])
// Selected songs (in setlist)
const selectedSongs = ref([])

// Update total duration whenever songs are reordered
const updateTotalDuration = () => {
    form.total_duration = selectedSongs.value.reduce((total, song) => total + song.duration_seconds, 0)
}

// Format duration for display
const formatDuration = (seconds) => {
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds % 3600) / 60)
    const remainingSeconds = seconds % 60

    if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`
    }
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

// Computed total duration formatted
const totalDurationFormatted = computed(() => formatDuration(form.total_duration))

const submit = () => {
    form.song_order = selectedSongs.value.map(song => song.id)
    form.post(route('setlists.store', props.band.id))
}
</script>

<template>
    <Head title="Create Setlist" />

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
                            :href="route('setlists.index', band.id)"
                            class="text-sm font-medium text-primary-600 hover:text-primary-700"
                        >
                            Setlists
                        </Link>
                    </div>
                    <h2 class="mt-1 text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Create New Setlist
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        Create a new setlist by dragging and dropping songs
                    </p>
                </div>
            </div>
        </template>

        <div class="max-w-7xl">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Error Message -->
                <DSAlert 
                    v-if="Object.keys(form.errors).length > 0" 
                    type="error"
                >
                    <ul class="list-disc list-inside">
                        <li v-for="error in form.errors" :key="error">{{ error }}</li>
                    </ul>
                </DSAlert>

                <!-- Basic Information -->
                <DSCard>
                    <div class="p-6 space-y-6">
                        <div>
                            <DSInput
                                v-model="form.name"
                                type="text"
                                label="Setlist Name"
                                :error="form.errors.name"
                                required
                            />
                            <p class="mt-1 text-sm text-neutral-500">
                                Give your setlist a memorable name
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-neutral-700">
                                Description (Optional)
                            </label>
                            <div class="mt-1">
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    :class="[
                                        'block w-full rounded-lg shadow-sm transition-colors duration-200',
                                        'border-neutral-300 focus:border-primary-500 focus:ring-primary-500',
                                        { 'border-error-500 focus:border-error-500 focus:ring-error-500': form.errors.description }
                                    ]"
                                />
                            </div>
                            <p class="mt-1 text-sm text-neutral-500">
                                Add any notes or context about this setlist
                            </p>
                        </div>
                    </div>
                </DSCard>

                <!-- Song Selection -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Available Songs -->
                    <DSCard>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-neutral-900">Available Songs</h3>
                                <span class="text-sm text-neutral-500">
                                    {{ availableSongs.length }} songs
                                </span>
                            </div>
                            <draggable
                                v-model="availableSongs"
                                :group="{ name: 'songs' }"
                                item-key="id"
                                class="min-h-[400px] border-2 border-dashed border-neutral-200 rounded-lg"
                                @change="updateTotalDuration"
                            >
                                <template #item="{ element }">
                                    <div class="flex items-center justify-between p-3 m-2 bg-white border border-neutral-200 rounded-lg cursor-move hover:shadow-sm transition-shadow duration-200">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-neutral-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                            </svg>
                                            <span class="font-medium text-neutral-900">{{ element.name }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-neutral-500">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ formatDuration(element.duration_seconds) }}
                                        </div>
                                    </div>
                                </template>
                                <template #footer v-if="availableSongs.length === 0">
                                    <div class="flex flex-col items-center justify-center h-40 text-neutral-500">
                                        <svg class="h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <p class="text-sm">All songs have been added to the setlist</p>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </DSCard>

                    <!-- Selected Songs -->
                    <DSCard>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-neutral-900">Setlist</h3>
                                <div class="flex items-center text-sm">
                                    <span class="text-neutral-500 mr-4">
                                        {{ selectedSongs.length }} songs
                                    </span>
                                    <span class="flex items-center text-neutral-900 font-medium">
                                        <svg class="h-4 w-4 mr-1 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ totalDurationFormatted }}
                                    </span>
                                </div>
                            </div>
                            <draggable
                                v-model="selectedSongs"
                                :group="{ name: 'songs' }"
                                item-key="id"
                                class="min-h-[400px] border-2 border-dashed border-neutral-200 rounded-lg"
                                @change="updateTotalDuration"
                            >
                                <template #item="{ element }">
                                    <div class="flex items-center justify-between p-3 m-2 bg-white border border-neutral-200 rounded-lg cursor-move hover:shadow-sm transition-shadow duration-200">
                                        <div class="flex items-center">
                                            <div class="flex items-center justify-center h-6 w-6 rounded-full bg-neutral-100 text-neutral-500 text-sm font-medium mr-3">
                                                {{ selectedSongs.indexOf(element) + 1 }}
                                            </div>
                                            <span class="font-medium text-neutral-900">{{ element.name }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-neutral-500">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ formatDuration(element.duration_seconds) }}
                                        </div>
                                    </div>
                                </template>
                                <template #footer v-if="selectedSongs.length === 0">
                                    <div class="flex flex-col items-center justify-center h-40 text-neutral-500">
                                        <svg class="h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                        </svg>
                                        <p class="text-sm">Drag songs here to add them to the setlist</p>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </DSCard>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <Link :href="route('setlists.index', band.id)">
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
                        :disabled="form.processing || selectedSongs.length === 0"
                    >
                        <span v-if="form.processing">Creating...</span>
                        <span v-else>Create Setlist</span>
                    </DSButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template> 
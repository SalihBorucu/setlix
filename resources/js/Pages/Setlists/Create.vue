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

// Add search functionality
const searchQuery = ref('')
const filteredAvailableSongs = computed(() => {
    if (!searchQuery.value) return availableSongs.value
    const query = searchQuery.value.toLowerCase()
    return availableSongs.value.filter(song => 
        song.name.toLowerCase().includes(query)
    )
})

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

// Add song to setlist
const addSong = (song) => {
    const newSong = { 
        ...song,
        pivot: {
            notes: '',
            order: selectedSongs.value.length
        }
    }
    selectedSongs.value.push(newSong)
    const index = availableSongs.value.findIndex(s => s.id === song.id)
    if (index !== -1) {
        availableSongs.value.splice(index, 1)
    }
    updateTotalDuration()
}

// Remove song from setlist
const removeSong = (songIndex) => {
    const removedSong = selectedSongs.value[songIndex]
    const cleanSong = { ...removedSong }
    delete cleanSong.pivot
    availableSongs.value.push(cleanSong)
    selectedSongs.value.splice(songIndex, 1)
    updateTotalDuration()
}

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
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-neutral-900">Available Songs</h3>
                            <span class="text-sm text-neutral-500">
                                {{ filteredAvailableSongs.length }} songs
                            </span>
                        </div>
                        
                        <!-- Search Input -->
                        <div class="mb-4">
                            <DSInput
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search songs..."
                                class="w-full"
                            >
                                <template #prefix>
                                    <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </template>
                            </DSInput>
                        </div>

                        <div class="min-h-[400px] rounded-lg border-2 border-dashed border-neutral-200 p-4 bg-neutral-50 overflow-y-auto">
                            <div v-for="song in filteredAvailableSongs" :key="song.id" 
                                class="flex items-center justify-between p-3 mb-2 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-neutral-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                    <span class="font-medium text-neutral-900">{{ song.name }}</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-neutral-500">
                                        {{ formatDuration(song.duration_seconds) }}
                                    </span>
                                    <button 
                                        type="button"
                                        @click="addSong(song)"
                                        class="p-1 rounded-full text-primary-600 hover:bg-primary-50"
                                        title="Add to setlist"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div v-if="filteredAvailableSongs.length === 0" class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-neutral-900">No songs found</h3>
                                <p class="mt-1 text-sm text-neutral-500">
                                    {{ searchQuery ? 'Try a different search term.' : 'All songs are in the setlist.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Songs -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-neutral-900">Setlist</h3>
                            <div class="flex items-center text-sm text-neutral-500">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Total Duration: {{ totalDurationFormatted }}
                            </div>
                        </div>
                        <draggable
                            v-model="selectedSongs"
                            item-key="id"
                            handle=".drag-handle"
                            class="min-h-[400px] rounded-lg border-2 border-dashed border-neutral-200 p-4"
                        >
                            <template #item="{ element, index }">
                                <div class="p-3 mb-2 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <span class="drag-handle cursor-move p-1 hover:bg-neutral-50 rounded mr-2">
                                                <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                                </svg>
                                            </span>
                                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary-100 text-primary-700 text-sm font-medium mr-2">
                                                {{ index + 1 }}
                                            </span>
                                            <span class="font-medium text-neutral-900">{{ element.name }}</span>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span class="text-sm text-neutral-500">
                                                {{ formatDuration(element.duration_seconds) }}
                                            </span>
                                            <button 
                                                type="button"
                                                @click="removeSong(index)"
                                                class="p-1 rounded-full text-error-600 hover:bg-error-50"
                                                title="Remove from setlist"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <input
                                            type="text"
                                            v-model="element.pivot.notes"
                                            placeholder="Add performance notes..."
                                            class="block w-full text-sm rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500"
                                        />
                                    </div>
                                </div>
                            </template>
                            <template #footer v-if="selectedSongs.length === 0">
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-neutral-900">No songs in setlist</h3>
                                    <p class="mt-1 text-sm text-neutral-500">Use the plus button to add songs to your setlist.</p>
                                </div>
                            </template>
                        </draggable>
                    </div>
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
<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard, DSInput, DSAlert, DSModal } from '@/Components/UI'
import { ref, computed } from 'vue'
import axios from 'axios'
import { DialogTitle } from '@headlessui/vue'

const props = defineProps({
    band: {
        type: Object,
        required: true
    }
})

const form = useForm({
    songs: [
        { name: '', duration: '', duration_seconds: '', url: '', artist: '' }
    ]
})

const errors = ref({})
const MAX_SONGS = 30

const canAddMore = computed(() => {
    return form.songs.length < MAX_SONGS
})

const addSong = () => {
    if (canAddMore.value) {
        form.songs.push({ name: '', duration: '', duration_seconds: '', url: '', artist: '' })
    }
}

const removeSong = (index) => {
    if (form.songs.length > 1) {
        form.songs.splice(index, 1)
    }
}

const formatDuration = (value) => {
    // Remove any non-digit characters
    const digits = value.replace(/\D/g, '')
    
    if (digits.length <= 2) {
        return digits
    }
    
    // Format as MM:SS
    const minutes = digits.slice(0, -2)
    const seconds = digits.slice(-2)
    return `${minutes}:${seconds}`
}

const parseDuration = (value) => {
    const [minutes, seconds] = value.split(':').map(Number)
    return (minutes * 60) + (seconds || 0)
}

const handleDurationInput = (index, event) => {
    const formatted = formatDuration(event.target.value)
    form.songs[index].duration = formatted
    form.songs[index].duration_seconds = parseDuration(formatted)
}

const showSpotifyModal = ref(false)
const spotifyUrl = ref('')
const isLoading = ref(false)
const spotifyError = ref(null)

const importFromSpotify = async () => {
    if (!spotifyUrl.value) return
    
    isLoading.value = true
    spotifyError.value = null
    
    try {
        const response = await axios.post(route('spotify.playlist-tracks'), {
            playlist_url: spotifyUrl.value
        })
        
        // Add tracks to the form
        const tracks = response.data.tracks
        form.songs = tracks.map(track => ({
            name: track.name,
            duration: track.duration,
            duration_seconds: track.duration_seconds,
            url: track.url,
            artist: track.artist
        }))
        
        showSpotifyModal.value = false
        spotifyUrl.value = ''
    } catch (error) {
        spotifyError.value = error.response?.data?.error || 'Failed to import playlist'
    } finally {
        isLoading.value = false
    }
}

const submit = () => {
    form.post(route('songs.bulk-store', props.band.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            form.songs = [{ name: '', duration: '', duration_seconds: '', url: '', artist: '' }]
        }
    })
}
</script>

<template>
    <Head :title="`Add Multiple Songs - ${band.name}`" />

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
                        Add Multiple Songs
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        Add up to {{ MAX_SONGS }} songs at once
                    </p>
                </div>
            </div>
        </template>

        <DSCard>
            <form @submit.prevent="submit" class="divide-y divide-neutral-200">
                <!-- Error Message -->
                <DSAlert 
                    v-if="Object.keys(form.errors).length > 0" 
                    type="error"
                    class="m-6"
                >
                    <ul class="list-disc list-inside">
                        <li v-for="error in form.errors" :key="error">{{ error }}</li>
                    </ul>
                </DSAlert>

                <!-- Songs List -->
                <div class="overflow-x-auto">
                    <div class="min-w-[800px]"> <!-- Minimum width container -->
                        <!-- Headers -->
                        <div class="grid grid-cols-12 gap-3 px-3 py-2 bg-neutral-50 border-b">
                            <div class="col-span-4">Song Name</div>
                            <div class="col-span-2">Duration</div>
                            <div class="col-span-3">Artist</div>
                            <div class="col-span-2">URL</div>
                            <div class="col-span-1"></div>
                        </div>
                        
                        <!-- Songs -->
                        <div 
                            v-for="(song, index) in form.songs" 
                            :key="index"
                            class="grid grid-cols-12 gap-3 px-3 py-2 border-b last:border-b-0"
                        >
                            <div class="col-span-4">
                                <DSInput
                                    v-model="song.name"
                                    type="text"
                                    placeholder="Song name"
                                    required
                                    class="w-full"
                                />
                            </div>
                            <div class="col-span-2">
                                <DSInput
                                    v-model="song.duration"
                                    type="text"
                                    placeholder="MM:SS"
                                    @input="handleDurationInput(index, $event)"
                                    required
                                    class="w-full"
                                />
                            </div>
                            <div class="col-span-3">
                                <DSInput
                                    v-model="song.artist"
                                    type="text"
                                    placeholder="Artist name"
                                    class="w-full"
                                />
                            </div>
                            <div class="col-span-2">
                                <DSInput
                                    v-model="song.url"
                                    type="url"
                                    placeholder="https://"
                                    class="w-full"
                                />
                            </div>
                            <div class="col-span-1 flex justify-end items-center">
                                <button 
                                    v-if="form.songs.length > 1"
                                    type="button"
                                    class="p-2 text-neutral-400 hover:text-danger-500"
                                    @click="removeSong(index)"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="p-4 md:p-6 flex flex-col sm:flex-row justify-between space-y-4 sm:space-y-0">
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                        <DSButton
                            type="button"
                            variant="secondary"
                            :disabled="!canAddMore"
                            @click="addSong"
                            class="w-full sm:w-auto"
                        >
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Another Song
                        </DSButton>

                        <DSButton
                            type="button"
                            variant="secondary"
                            @click="showSpotifyModal = true"
                            class="w-full sm:w-auto"
                        >
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                            </svg>
                            Import from Spotify
                        </DSButton>
                    </div>

                    <div class="flex space-x-3">
                        <Link 
                            :href="route('songs.index', band.id)" 
                            class="flex-1 sm:flex-none"
                        >
                            <DSButton 
                                variant="outline" 
                                class="w-full sm:w-auto"
                            >
                                Cancel
                            </DSButton>
                        </Link>
                        <DSButton
                            type="submit"
                            variant="primary"
                            :disabled="form.processing"
                            class="flex-1 sm:w-auto"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save All Songs</span>
                        </DSButton>
                    </div>
                </div>
            </form>
        </DSCard>

        <!-- Spotify Import Modal -->
        <DSModal
            :show="showSpotifyModal"
            @close="showSpotifyModal = false"
            max-width="lg"
        >
            <DialogTitle as="h3" class="text-lg font-medium leading-6 text-neutral-900 mb-4">
                Import from Spotify Playlist
            </DialogTitle>

            <div class="space-y-4">
                <p class="text-sm text-neutral-600">
                    Enter a Spotify playlist URL or ID to import its tracks.
                </p>

                <DSAlert
                    v-if="spotifyError"
                    type="error"
                    class="mb-4"
                >
                    {{ spotifyError }}
                </DSAlert>

                <DSInput
                    v-model="spotifyUrl"
                    label="Playlist URL"
                    placeholder="https://open.spotify.com/playlist/..."
                    :disabled="isLoading"
                />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <DSButton
                    variant="outline"
                    @click="showSpotifyModal = false"
                    :disabled="isLoading"
                >
                    Cancel
                </DSButton>
                <DSButton
                    variant="primary"
                    @click="importFromSpotify"
                    :disabled="!spotifyUrl || isLoading"
                >
                    <span v-if="isLoading">Importing...</span>
                    <span v-else>Import Tracks</span>
                </DSButton>
            </div>
        </DSModal>
    </AuthenticatedLayout>
</template> 
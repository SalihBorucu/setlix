<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard, DSInput, DSAlert } from '@/Components/UI'
import { ref, computed } from 'vue'

const props = defineProps({
    band: {
        type: Object,
        required: true
    }
})

const form = useForm({
    songs: [
        { name: '', duration: '', duration_seconds: '' }
    ]
})

const errors = ref({})
const MAX_SONGS = 30

const canAddMore = computed(() => {
    return form.songs.length < MAX_SONGS
})

const addSong = () => {
    if (canAddMore.value) {
        form.songs.push({ name: '', duration: '', duration_seconds: '' })
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

const submit = () => {
    form.post(route('songs.bulk-store', props.band.id), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            form.songs = [{ name: '', duration: '', duration_seconds: '' }]
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
                <div 
                    v-for="(song, index) in form.songs" 
                    :key="index"
                    class="p-2 flex items-center space-x-4"
                >
                    <div class="flex-1">
                        <DSInput
                            v-model="song.name"
                            type="text"
                            :label="index === 0 ? 'Song Name' : ''"
                            placeholder="Song name"
                            required
                        />
                    </div>
                    <div class="w-32">
                        <DSInput
                            v-model="song.duration"
                            type="text"
                            :label="index === 0 ? 'Duration' : ''"
                            placeholder="MM:SS"
                            @input="handleDurationInput(index, $event)"
                            required
                        />
                    </div>
                    <div class="flex items-end">
                        <button 
                            v-if="form.songs.length > 1"
                            type="button"
                            class="h-10 p-2 text-neutral-400 hover:text-danger-500"
                            @click="removeSong(index)"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Actions -->
                <div class="p-6 flex items-center justify-between">
                    <DSButton
                        type="button"
                        variant="secondary"
                        :disabled="!canAddMore"
                        @click="addSong"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Another Song
                    </DSButton>

                    <div class="flex space-x-3">
                        <Link :href="route('songs.index', band.id)">
                            <DSButton variant="outline">
                                Cancel
                            </DSButton>
                        </Link>
                        <DSButton
                            type="submit"
                            variant="primary"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save All Songs</span>
                        </DSButton>
                    </div>
                </div>
            </form>
        </DSCard>
    </AuthenticatedLayout>
</template> 
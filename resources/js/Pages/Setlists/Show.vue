<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard } from '@/Components/UI'
import { ref, computed } from 'vue'

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    setlist: {
        type: Object,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    }
})

const performanceMode = ref(false)

const confirmDelete = () => {
    if (confirm('Are you sure you want to delete this setlist? This action cannot be undone.')) {
        router.delete(route('setlists.destroy', [props.band.id, props.setlist.id]))
    }
}

const formatDuration = (seconds) => {
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds % 3600) / 60)
    const remainingSeconds = seconds % 60

    if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`
    }
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

// Sort songs by their order in the setlist
const sortedSongs = computed(() => {
    return [...props.setlist.songs].sort((a, b) => a.pivot.order - b.pivot.order)
})
</script>

<template>
    <Head :title="`${setlist.name} - ${band.name}`" />

    <AuthenticatedLayout v-if="!performanceMode">
        <template #header>
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1" v-if="!performanceMode">
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
                    </div>
                    <h2 class="mt-1 text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        {{ setlist.name }}
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        {{ setlist.songs?.length || 0 }} songs · {{ formatDuration(setlist.total_duration) }} total duration
                    </p>
                </div>
                <div class="mt-4 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3 md:ml-4 md:mt-0">
                    <DSButton
                        :variant="performanceMode ? 'primary' : 'secondary'"
                        @click="performanceMode = !performanceMode"
                        class="w-full sm:w-auto"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!performanceMode" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ performanceMode ? 'Exit Performance Mode' : 'Performance Mode' }}
                    </DSButton>
                    <template v-if="isAdmin">
                        <Link :href="route('setlists.edit', [band.id, setlist.id])" class="w-full sm:w-auto">
                            <DSButton variant="secondary" class="w-full sm:w-auto" v-if="!performanceMode">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Edit Setlist
                            </DSButton>
                        </Link>
                        <DSButton
                            v-if="!performanceMode"
                            variant="danger"
                            @click="confirmDelete"
                            class="w-full sm:w-auto"
                        >
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Setlist
                        </DSButton>
                    </template>
                </div>
            </div>
        </template>

        <!-- Normal Mode -->
        <div v-if="!performanceMode" class="grid gap-6 md:grid-cols-2">
            <DSCard>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">About</h3>
                    </div>
                    <dl class="divide-y divide-neutral-200">
                        <!-- Duration -->
                        <div class="py-4">
                            <dt class="text-sm font-medium text-neutral-500">Total Duration</dt>
                            <dd class="mt-1 flex items-center text-sm text-neutral-900">
                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ formatDuration(setlist.total_duration) }}
                            </dd>
                        </div>

                        <!-- Song Count -->
                        <div class="py-4">
                            <dt class="text-sm font-medium text-neutral-500">Songs</dt>
                            <dd class="mt-1 flex items-center text-sm text-neutral-900">
                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                                {{ setlist.songs?.length || 0 }} songs
                            </dd>
                        </div>

                        <!-- Created At -->
                        <div class="py-4">
                            <dt class="text-sm font-medium text-neutral-500">Created</dt>
                            <dd class="mt-1 flex items-center text-sm text-neutral-900">
                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ setlist.formatted_created_at }}
                            </dd>
                        </div>
                    </dl>

                    <!-- Description -->
                    <div class="mt-6">
                        <h4 class="text-sm font-medium text-neutral-500">Description</h4>
                        <p class="mt-2 text-sm text-neutral-900 whitespace-pre-line">
                            {{ setlist.description || 'No description provided.' }}
                        </p>
                    </div>
                </div>
            </DSCard>

            <!-- Songs List -->
            <DSCard>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">Songs</h3>
                        <Link v-if="isAdmin" :href="route('setlists.edit', [band.id, setlist.id])">
                            <DSButton variant="secondary" size="sm">
                                Reorder Songs
                            </DSButton>
                        </Link>
                    </div>
                    <div class="divide-y divide-neutral-200">
                        <div
                            v-for="(song, index) in sortedSongs"
                            :key="song.id"
                            class="py-4"
                        >
                            <div class="flex items-start justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-neutral-900">{{ index + 1 }}. {{ song.name }}</span>
                                    </div>
                                    <div class="mt-1 text-xs text-neutral-500">{{ song.formatted_duration }}</div>
                                    <div v-if="song.pivot.notes" class="mt-2 text-sm text-neutral-600">
                                        {{ song.pivot.notes }}
                                    </div>
                                </div>
                                <div class="ml-4 flex items-center space-x-2">
                                    <a
                                        v-if="song.url"
                                        :href="song.url"
                                        target="_blank"
                                        class="text-neutral-400 hover:text-neutral-500"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <Link
                                        v-if="song.document_path"
                                        :href="route('songs.document', [band.id, song.id])"
                                        class="text-neutral-400 hover:text-neutral-500"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </Link>
                                    <Link
                                        :href="route('songs.show', [band.id, song.id])"
                                        class="text-neutral-400 hover:text-neutral-500"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </DSCard>
        </div>
    </AuthenticatedLayout>

     <!-- Performance Mode -->
     <div v-if="performanceMode" class="space-y-6">
            <DSCard class="bg-gray-900">
                <div class="p-6">
                    <DSButton
                        :variant="performanceMode ? 'primary' : 'secondary'"
                        @click="performanceMode = !performanceMode"
                        class="w-full sm:w-auto mb-4"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!performanceMode" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        'Exit Performance Mode'
                </DSButton>
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-white">{{ setlist.name }}</h3>
                            <p class="mt-1 text-neutral-400">
                                {{ setlist.songs?.length || 0 }} songs · {{ formatDuration(setlist.total_duration) }}
                            </p>
                        </div>
                        <div class="text-xl text-white">
                            {{ formatDuration(setlist.total_duration) }}
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div
                            v-for="(song, index) in sortedSongs"
                            :key="song.id"
                            class="rounded-lg border border-neutral-700 bg-neutral-800/50 p-6"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center">
                                        <span class="text-xl font-semibold text-white">{{ index + 1 }}. {{ song.name }}</span>
                                    </div>
                                    <div class="mt-1 text-neutral-400">{{ song.formatted_duration }}</div>
                                    <div v-if="song.pivot.notes" class="mt-4 text-sm text-neutral-300 whitespace-pre-line">
                                        {{ song.pivot.notes }}
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <a
                                        v-if="song.url"
                                        :href="song.url"
                                        target="_blank"
                                        class="text-primary-400 hover:text-primary-300"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <Link
                                        v-if="song.document_path"
                                        :href="route('songs.document', [band.id, song.id])"
                                        class="text-primary-400 hover:text-primary-300"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </DSCard>
        </div>
</template> 
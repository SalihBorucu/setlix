<script setup>
import { DSInput } from '@/Components/UI'

const props = defineProps({
    songs: {
        type: Array,
        required: true
    },
    searchQuery: {
        type: String,
        required: true
    }
})

const emit = defineEmits(['update:searchQuery', 'addSong'])

// Format duration helper
const formatDuration = (seconds) => {
    const minutes = Math.floor(seconds / 60)
    const remainingSeconds = seconds % 60
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-neutral-900">Available Songs</h3>
            <span class="text-sm text-neutral-500">
                {{ songs.length }} songs
            </span>
        </div>
        
        <!-- Search Input -->
        <div class="mb-4">
            <DSInput
                :model-value="searchQuery"
                @update:model-value="emit('update:searchQuery', $event)"
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
            <div v-for="song in songs" :key="song.id" 
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
                        @click="emit('addSong', song)"
                        class="p-1 rounded-full text-primary-600 hover:bg-primary-50"
                        title="Add to setlist"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <div v-if="songs.length === 0" class="text-center py-12">
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
</template> 
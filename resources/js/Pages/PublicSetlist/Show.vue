<template>
    <div class="min-h-screen bg-gray-100">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Header -->
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">{{ setlist.name }}</h1>
                            <p v-if="setlist.description" class="mt-2 text-gray-600">{{ setlist.description }}</p>
                            
                            <!-- Duration Information -->
                            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-sm font-medium text-gray-900">
                                        Target Duration: {{ formatDuration(targetDuration) }}
                                    </div>
                                    <div class="text-sm font-medium" :class="currentDuration > targetDuration ? 'text-red-600' : 'text-gray-900'">
                                        Current Duration: {{ formatDuration(currentDuration) }}
                                    </div>
                                </div>
                                
                                <!-- Progress Bar -->
                                <div class="relative w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div 
                                        class="absolute top-0 left-0 h-full transition-all duration-300"
                                        :class="currentDuration > targetDuration ? 'bg-red-500' : 'bg-green-500'"
                                        :style="{ width: `${Math.min((currentDuration / targetDuration) * 100, 100)}%` }"
                                    ></div>
                                </div>
                                
                                <!-- Duration Status -->
                                <p class="mt-2 text-sm" :class="currentDuration > targetDuration ? 'text-red-600' : 'text-gray-500'">
                                    {{ 
                                        currentDuration > targetDuration 
                                            ? `Over target by ${formatDuration(currentDuration - targetDuration)}` 
                                            : currentDuration < targetDuration
                                                ? `Under target by ${formatDuration(targetDuration - currentDuration)}`
                                                : 'Perfect match!'
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Setlist Items -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Available Songs -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-lg font-medium text-gray-900">Available Songs</h2>
                                    <button
                                        @click="showBreakModal = true"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Add Break
                                    </button>
                                </div>
                                <div class="space-y-2">
                                    <div v-for="song in availableSongsList" :key="song.id" class="flex items-center justify-between p-3 bg-white border rounded-lg">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ song.name }}</div>
                                            <div class="text-sm text-gray-500">{{ formatDuration(song.duration_seconds) }}</div>
                                        </div>
                                        <button
                                            @click="addSong(song)"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Setlist -->
                            <div>
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Your Setlist</h2>
                                <draggable
                                    v-model="items"
                                    item-key="id"
                                    handle=".handle"
                                    @change="handleDragChange"
                                    class="space-y-2"
                                >
                                    <template #item="{ element }">
                                        <div class="flex flex-col p-4 rounded-lg space-y-2" :class="element.type === 'break' ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50'">
                                            <div class="flex items-center space-x-4">
                                                <div class="handle cursor-move">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <div class="font-medium text-gray-900">
                                                            {{ element.type === 'break' ? element.title : element.song?.name }}
                                                        </div>
                                                        <span v-if="element.type === 'break'" class="px-2 py-0.5 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">
                                                            Break
                                                        </span>
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ formatDuration(element.duration_seconds) }}</div>
                                                </div>
                                                <button
                                                    @click="removeItem(element)"
                                                    class="text-red-600 hover:text-red-800 p-2"
                                                >
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="w-full">
                                                <textarea
                                                    v-model="element.notes"
                                                    :placeholder="element.type === 'break' ? 'Add break notes or announcements...' : 'Add performance notes...'"
                                                    class="w-full px-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 resize-y min-h-[60px]"
                                                    rows="3"
                                                ></textarea>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </div>

                        <!-- Break Modal -->
                        <BreakModal
                            :is-open="showBreakModal"
                            @close="showBreakModal = false"
                            @create="addBreak"
                        />

                        <!-- Client Information Form -->
                        <div class="mt-8 border-t pt-6">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Your Information</h2>
                            
                            <!-- Duration Warning -->
                            <div v-if="currentDuration > targetDuration" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-sm text-red-700">
                                        Please adjust your selection to meet the target duration before submitting.
                                        You are over by {{ formatDuration(currentDuration - targetDuration) }}.
                                    </p>
                                </div>
                            </div>

                            <form @submit.prevent="submitForm" class="space-y-4">
                                <div>
                                    <label for="client_name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input
                                        type="text"
                                        id="client_name"
                                        v-model="form.client_name"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    >
                                </div>
                                <div>
                                    <label for="client_email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input
                                        type="email"
                                        id="client_email"
                                        v-model="form.client_email"
                                        required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    >
                                </div>
                                <div class="flex justify-end">
                                    <button
                                        type="submit"
                                        :disabled="currentDuration > targetDuration || form.processing"
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        :class="[
                                            currentDuration > targetDuration 
                                                ? 'bg-gray-400 cursor-not-allowed' 
                                                : 'bg-indigo-600 hover:bg-indigo-700'
                                        ]"
                                    >
                                        <span v-if="form.processing">Submitting...</span>
                                        <span v-else-if="currentDuration > targetDuration">Duration Exceeded</span>
                                        <span v-else>Submit Setlist</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import draggable from 'vuedraggable'
import BreakModal from '@/Components/Setlists/BreakModal.vue'

const props = defineProps({
    setlist: Object,
    targetDuration: Number,
    availableSongs: Array
})

const items = ref(props.setlist.items)
const availableSongsList = ref([...props.availableSongs])
const showBreakModal = ref(false)
const form = useForm({
    client_name: '',
    client_email: '',
    items: []
})

// Compute current duration whenever items change
const currentDuration = computed(() => {
    return items.value.reduce((total, item) => {
        return total + (item.duration_seconds || 0)
    }, 0)
})

// Add break to setlist
const addBreak = (breakData) => {
    items.value.push({
        id: `break-${Date.now()}`, // Temporary ID for draggable
        type: 'break',
        title: breakData.title,
        duration_seconds: parseInt(breakData.duration_seconds) || 0,
        notes: breakData.notes || '',
        order: items.value.length
    })
}

// Add song to setlist
const addSong = (song) => {
    items.value.push({
        id: song.id,
        type: 'song',
        song: song,
        duration_seconds: song.duration_seconds,
        notes: '',
        order: items.value.length
    })
    // Remove from available songs
    const index = availableSongsList.value.findIndex(s => s.id === song.id)
    if (index !== -1) {
        availableSongsList.value.splice(index, 1)
    }
}

// Remove item from setlist
const removeItem = (item) => {
    const index = items.value.findIndex(i => i.id === item.id)
    if (index !== -1) {
        const removedItem = items.value[index]
        if (removedItem.type === 'song' && removedItem.song) {
            // Add back to available songs
            availableSongsList.value.push(removedItem.song)
        }
        items.value.splice(index, 1)
        // Update order of remaining items
        items.value.forEach((item, index) => {
            item.order = index
        })
    }
}

const handleDragChange = () => {
    // Update order of items
    items.value.forEach((item, index) => {
        item.order = index
    })
}

const submitForm = () => {
    form.items = items.value.map(item => ({
        type: item.type,
        song_id: item.type === 'song' ? item.song.id : null,
        title: item.type === 'break' ? item.title : null,
        duration_seconds: item.duration_seconds,
        notes: item.notes,
        order: item.order
    }))

    form.post(route('public.setlist.submit', props.setlist.public_slug))
}

const formatDuration = (seconds) => {
    if (!seconds) return '0:00'
    const minutes = Math.floor(seconds / 60)
    const remainingSeconds = seconds % 60
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}
</script> 
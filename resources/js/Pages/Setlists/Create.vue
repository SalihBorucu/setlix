<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSAlert } from '@/Components/UI'
import { ref, computed } from 'vue'

// Import our new components
import SetlistHeader from '@/Components/Setlists/SetlistHeader.vue'
import SetlistBasicInfo from '@/Components/Setlists/SetlistBasicInfo.vue'
import AvailableSongsList from '@/Components/Setlists/AvailableSongsList.vue'
import SetlistItemsList from '@/Components/Setlists/SetlistItemsList.vue'
import BreakModal from '@/Components/Setlists/BreakModal.vue'

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
    target_duration: '',
    target_duration_seconds: 0,
    items: [],
    total_duration: 0
})

// Available songs (not in setlist)
const availableSongs = ref([...props.songs])

// Selected items (songs and breaks in setlist)
const selectedItems = ref([])

// Break modal state
const showBreakModal = ref(false)

// Add search functionality
const searchQuery = ref('')
const filteredAvailableSongs = computed(() => {
    if (!searchQuery.value) return availableSongs.value
    const query = searchQuery.value.toLowerCase()
    return availableSongs.value.filter(song =>
        song.name.toLowerCase().includes(query)
    )
})

// Update total duration whenever items are reordered
const updateTotalDuration = () => {
    form.total_duration = selectedItems.value.reduce((total, item) => {
        return total + (item.duration_seconds || 0)
    }, 0)
}

// Add break to setlist
const addBreak = (breakData) => {
    selectedItems.value.push({
        id: `break-${Date.now()}`, // Temporary ID for draggable
        ...breakData,
        pivot: {
            notes: breakData.notes,
            order: selectedItems.value.length
        }
    })
    updateTotalDuration()
}

// Add song to setlist
const addSong = (song) => {
    const newItem = {
        ...song,
        type: 'song',
        pivot: {
            notes: '',
            order: selectedItems.value.length
        }
    }
    selectedItems.value.push(newItem)
    const index = availableSongs.value.findIndex(s => s.id === song.id)
    if (index !== -1) {
        availableSongs.value.splice(index, 1)
    }
    updateTotalDuration()
}

// Remove item from setlist
const removeItem = (index) => {
    const removedItem = selectedItems.value[index]
    if (removedItem.type === 'song') {
        const cleanSong = { ...removedItem }
        delete cleanSong.pivot
        availableSongs.value.push(cleanSong)
    }
    selectedItems.value.splice(index, 1)
    updateTotalDuration()
}

// Update notes for an item
const updateNotes = ({ index, notes }) => {
    if (selectedItems.value[index]) {
        selectedItems.value[index].pivot.notes = notes
    }
}

// Update submit to use target_duration_seconds
const submit = () => {
    form.items = selectedItems.value.map(item => {
        const formattedItem = {
            type: item.type,
            duration_seconds: item.duration_seconds,
            notes: item.pivot.notes
        }

        if (item.type === 'song') {
            formattedItem.song_id = item.id
            formattedItem.title = null
        } else {
            formattedItem.song_id = null
            formattedItem.title = item.title
        }

        return formattedItem
    })

    console.log('Submitting items:', form.items)

    form.post(route('setlists.store', props.band.id), {
        preserveScroll: true,
        onError: (errors) => {
            console.error('Form errors:', errors)
        }
    })
}
</script>

<template>
    <Head title="Create Setlist" />

    <AuthenticatedLayout>
        <template #header>
            <SetlistHeader :band="band" :form="form" />
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
                <SetlistBasicInfo :form="form" />

                <!-- Song Selection -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Available Songs -->
                    <AvailableSongsList
                        :songs="filteredAvailableSongs"
                        v-model:search-query="searchQuery"
                        @add-song="addSong"
                    />

                    <!-- Selected Items -->
                    <SetlistItemsList
                        v-model:items="selectedItems"
                        :total-duration="form.total_duration"
                        @remove="removeItem"
                        @update-notes="updateNotes"
                        @add-break="showBreakModal = true"
                    />
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
                        :disabled="form.processing || selectedItems.length === 0"
                    >
                        <span v-if="form.processing">Creating...</span>
                        <span v-else>Create Setlist</span>
                    </DSButton>
                </div>
            </form>
        </div>

        <!-- Break Modal -->
        <BreakModal
            :is-open="showBreakModal"
            @close="showBreakModal = false"
            @create="addBreak"
        />
    </AuthenticatedLayout>
</template>

<script setup>
import { DSButton } from '@/Components/UI'
import draggable from 'vuedraggable'
import SetlistItem from './SetlistItem.vue'

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
    totalDuration: {
        type: Number,
        required: true
    }
})

const emit = defineEmits(['update:items', 'remove', 'updateNotes', 'addBreak'])

// Format duration helper
const formatDuration = (seconds) => {
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds % 3600) / 60)
    const remainingSeconds = seconds % 60

    if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`
    }
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}
</script>

<template>
    <div>
        <!-- Add Break Button -->
        <div class="flex justify-end mb-4">
            <DSButton
                type="button"
                variant="outline"
                @click="emit('addBreak')"
            >
                <template #prefix>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </template>
                Add Break
            </DSButton>
        </div>

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-neutral-900">Setlist</h3>
            <div class="flex items-center text-sm text-neutral-500">
                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Total Duration: {{ formatDuration(totalDuration) }}
            </div>
        </div>

        <draggable
            :model-value="items"
            @update:model-value="emit('update:items', $event)"
            item-key="id"
            handle=".drag-handle"
            class="min-h-[400px] rounded-lg border-2 border-dashed border-neutral-200 p-4"
        >
            <template #item="{ element, index }">
                <SetlistItem
                    :item="element"
                    :index="index"
                    @remove="emit('remove', $event)"
                    @update-notes="emit('updateNotes', $event)"
                />
            </template>

            <template #footer v-if="items.length === 0">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-neutral-900">No items in setlist</h3>
                    <p class="mt-1 text-sm text-neutral-500">Add songs or breaks to your setlist.</p>
                </div>
            </template>
        </draggable>
    </div>
</template> 
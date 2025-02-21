<script setup>
const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    index: {
        type: Number,
        required: true
    }
})

const emit = defineEmits(['remove', 'updateNotes'])

// Format duration helper
const formatDuration = (seconds) => {
    const minutes = Math.floor(seconds / 60)
    const remainingSeconds = seconds % 60
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}
</script>

<template>
    <div class="mb-4">
        <div :class="[
            'p-3 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200',
            item.type === 'break' 
                ? 'bg-neutral-50 border-2 border-dashed border-neutral-200' 
                : 'bg-white border border-neutral-200'
        ]">
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
                    <div class="flex items-center">
                        <div v-if="item.type === 'break'" 
                            class="flex items-center px-2 py-1 rounded-md bg-neutral-100 text-neutral-700 mr-2"
                        >
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span class="text-xs font-medium">BREAK</span>
                        </div>
                        <span class="font-medium text-neutral-900">
                            {{ item.type === 'break' ? item.title : item.name }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-neutral-500">
                        {{ formatDuration(item.duration_seconds) }}
                    </span>
                    <button 
                        type="button"
                        @click="emit('remove', index)"
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
                    :value="item.pivot.notes"
                    @input="emit('updateNotes', { index, notes: $event.target.value })"
                    :placeholder="item.type === 'break' ? 'Add break notes...' : 'Add performance notes...'"
                    class="block w-full text-sm rounded-lg border-neutral-300 focus:border-primary-500 focus:ring-primary-500"
                />
            </div>
        </div>
    </div>
</template> 
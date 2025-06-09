<script setup>

import {DSButton} from "@/Components/UI/index.js";

const props = defineProps({
    fileGroups: {
        type: Array,
        required: true
    }
})

const emit = defineEmits(['add-file-group', 'remove-file-group', 'handle-file-upload']);
</script>

<template>
    <!-- File Upload Section -->
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-neutral-900">Files</h3>
            <DSButton
                type="button"
                variant="secondary"
                @click="emit('add-file-group')"
                :disabled="fileGroups.length >= 10"
            >
                Add File
            </DSButton>
        </div>

        <div class="space-y-2">
            <div
                v-for="(group, index) in fileGroups"
                :key="index"
                class="flex items-center gap-4 border rounded-md p-3"
            >
                <div class="flex-1">
                    <select
                        v-model="group.type"
                        class="block w-full rounded-md border-neutral-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value="">Select Type</option>
                        <option value="lyrics">Lyrics</option>
                        <option value="notes">Notes</option>
                        <option value="chords">Chords</option>
                        <option value="tabs">Tabs</option>
                        <option value="sheet_music">Sheet Music</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="flex-1">
                    <select
                        v-model="group.instrument"
                        class="block w-full rounded-md border-neutral-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    >
                        <option value="">Select Instrument</option>
                        <option value="vocals">Vocals</option>
                        <option value="guitar">Guitar</option>
                        <option value="saxophone">Saxophone</option>
                        <option value="trumpet">Trumpet</option>
                        <option value="trombone">Trombone</option>
                        <option value="flute">Flute</option>
                        <option value="keyboard">Keyboard</option>
                        <option value="drums">Drums</option>
                        <option value="bass">Bass</option>
                        <option value="violin">Violin</option>
                        <option value="cello">Cello</option>
                        <option value="percussion">Percussion</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="flex-1">
                    <input
                        type="file"
                        @change="emit('handle-file-upload', $event, index)"
                        accept=".pdf,.txt,.musicxml"
                        class="block w-full text-sm"
                    >
                </div>

                <button
                    type="button"
                    @click="emit('remove-file-group', index)"
                    class="text-neutral-400 hover:text-neutral-500"
                >
                    <span class="sr-only">Remove</span>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <p class="text-sm text-neutral-500">PDF or TXT or MusicXML files up to 10MB</p>
    </div>
</template>

<style scoped>

</style>

<script setup>
import { DSButton, DSCard } from "@/Components/UI";
import { Link } from '@inertiajs/vue3';
import visitExternalLink from "@/Utilities/visitExternalLink.js";

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    setlist: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['exit']);

const formatDuration = (seconds) => {
    const hours = Math.floor(seconds / 3600)
    const minutes = Math.floor((seconds % 3600) / 60)
    const remainingSeconds = seconds % 60

    if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`
    }
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

const visitLyricsUrl = (song) => {
    let url = "https://www.google.com/search?q="
    url += encodeURIComponent(song.name)
    if (song.artist) {
        url += (' ' + encodeURIComponent(song.artist))
    }
    url += "+lyrics"
    return visitExternalLink(url, true);
}

const handleFileDownload = (file, song) => {
    visitExternalLink(
        route('songs.files.download', [props.band.id, song.id, file.id]),
        true,
        true
    );
};
</script>

<template>
    <div class="space-y-6 flex justify-center">
        <DSCard bg-color="bg-gray-900 h-full lg:w-1/2 w-full">
            <div class="p-6">
                <DSButton
                    variant="primary"
                    @click="$emit('exit')"
                    class="w-full sm:w-auto mb-4"
                >
                    Exit Performance Mode
                </DSButton>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-white">{{ setlist.name }}</h3>
                        <p class="mt-1 text-neutral-400">
                            {{ setlist.items?.length || 0 }} items · {{ formatDuration(setlist.total_duration) }}
                        </p>
                    </div>
                    <div class="text-xl text-white">
                        {{ formatDuration(setlist.total_duration) }}
                    </div>
                </div>
                <div class="space-y-4">
                    <div
                        v-for="(item, index) in setlist.items"
                        :key="item.id"
                        :class="[
                            'rounded-lg border p-1',
                            item.type === 'break' 
                                ? 'border-neutral-600 bg-neutral-700/50' 
                                : 'border-neutral-700 bg-neutral-800/50'
                        ]"
                    >
                        <div class="flex flex-col space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <!-- Break Badge -->
                                    <div v-if="item.type === 'break'" 
                                        class="flex items-center px-2 py-1 rounded-md bg-neutral-600 text-neutral-200"
                                    >
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        <span class="text-xs font-medium">BREAK</span>
                                    </div>

                                    <span class="text-base font-semibold text-white">
                                        <template v-if="item.type === 'song'">
                                            {{ setlist.items.filter(i => i.type === 'song' && i.order <= item.order).length }}.
                                        </template>
                                        {{ item.type === 'break' ? item.title : item.song.name }}
                                    </span>
                                </div>
                                <span class="text-sm text-neutral-500">
                                        {{ formatDuration(item.duration_seconds) }}
                                </span>
                            </div>

                            <div v-if="item.notes" 
                                class="text-sm text-neutral-400 pl-4 -mt-1"
                            >
                                {{ item.notes }}
                            </div>

                            <!-- Song Actions -->
                            <div v-if="item.type === 'song'" class="flex flex-wrap gap-1.5">
                                <Link 
                                    v-if="item.song.url"
                                    :href="item.song.url"
                                    target="_blank"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-primary-400 hover:text-primary-300"
                                >
                                    → Url
                                </Link>

                                <button
                                    @click="visitLyricsUrl(item.song)"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-primary-400 hover:text-primary-300"
                                >
                                    ○ Lyrics
                                </button>

                                <button
                                    v-for="file in item.song.files"
                                    :key="file.id"
                                    @click="handleFileDownload(file, item.song)"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-primary-400 hover:text-primary-300"
                                >
                                    □ {{ file.type.charAt(0).toUpperCase() + file.type.slice(1) }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </DSCard>
    </div>
</template> 
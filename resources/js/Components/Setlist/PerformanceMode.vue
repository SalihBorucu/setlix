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
    },
    sortedSongs: {
        type: Array,
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
    <div class="space-y-6">
        <DSCard bg-color="bg-gray-900 h-full">
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
                        class="rounded-lg border border-neutral-700 bg-neutral-800/50 p-2"
                    >
                        <div class="flex flex-col space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="text-base font-semibold text-white">
                                        {{ index + 1 }}. {{ song.name }}
                                    </span>
                                    <span class="text-sm text-neutral-500">
                                        {{ song.formatted_duration }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="song.pivot.notes" 
                                class="text-sm text-neutral-400 pl-4 -mt-1"
                            >
                                {{ song.pivot.notes }}
                            </div>

                            <div class="flex flex-wrap gap-1.5">
                                <Link 
                                    v-if="song.url"
                                    :href="song.url"
                                    target="_blank"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-primary-400 hover:text-primary-300"
                                >
                                    → Spotify
                                </Link>

                                <button
                                    @click="visitLyricsUrl(song)"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-primary-400 hover:text-primary-300"
                                >
                                    ○ Lyrics
                                </button>

                                <button
                                    v-for="file in song.files"
                                    :key="file.id"
                                    @click="handleFileDownload(file, song)"
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
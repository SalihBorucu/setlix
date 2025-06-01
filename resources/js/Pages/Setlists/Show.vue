<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard, DSAlertModal } from '@/Components/UI'
import PerformanceMode from '@/Components/Setlist/PerformanceMode.vue'
import { ref, computed } from 'vue'
import visitExternalLink from "@/Utilities/visitExternalLink.js";
import axios from 'axios';

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
const deleteModal = ref(null)

const confirmDelete = () => {
    deleteModal.value.open()
}

const handleDelete = () => {
    router.delete(route('setlists.destroy', [props.band.id, props.setlist.id]))
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

const handleExportPdf = async () => {
    try {
        const response = await axios.get(route('setlists.export', [props.band.id, props.setlist.id]));
        if (response.data.success && response.data.download_url) {
            visitExternalLink(response.data.download_url, false, true);
        }
    } catch (error) {
        console.error('Error exporting PDF:', error);
    }
};
</script>

<template>
    <Head :title="`${setlist.name} - ${band.name}`" />

    <PerformanceMode
        v-if="performanceMode"
        :band="band"
        :setlist="setlist"
        @exit="performanceMode = false"
    />

    <AuthenticatedLayout v-else>
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
                    <h2 class="mt-1 text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight test">
                        {{ setlist.name }}
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        {{ setlist.items?.length || 0 }} items ({{ setlist.items?.filter(i => i.type === 'song').length || 0 }} songs)
                    </p>
                </div>
                <div class="mt-4 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3 md:ml-4 md:mt-0">
                    <DSButton
                        variant="primary"
                        @click="performanceMode = !performanceMode"
                        class="w-full sm:w-auto"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!performanceMode" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ performanceMode ? 'Exit Performance Mode' : 'Performance Mode' }}
                    </DSButton>

                    <DSButton
                        variant="secondary"
                        class="w-full sm:w-auto"
                        @click="handleExportPdf"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
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
                                {{ setlist.items?.length || 0 }} items ({{ setlist.items?.filter(i => i.type === 'song').length || 0 }} songs)
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

                        <!-- Description -->
                        <div class="py-4">
                            <h4 class="text-sm font-medium text-neutral-500">Description</h4>
                            <p class="mt-2 text-sm text-neutral-900 whitespace-pre-line">
                                {{ setlist.description || 'No description provided.' }}
                            </p>
                        </div>
                    </dl>

                </div>
            </DSCard>

            <!-- Songs List -->
            <DSCard>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">Setlist Items</h3>
                        <Link v-if="isAdmin" :href="route('setlists.edit', [band.id, setlist.id])">
                            <DSButton variant="secondary" size="sm">
                                Edit Setlist
                            </DSButton>
                        </Link>
                    </div>
                    <div class="divide-y divide-neutral-200">
                        <div
                            v-for="(item, index) in setlist.items"
                            :key="item.id"
                            :class="[
                                'py-4',
                                item.type === 'break' ? 'bg-neutral-50 px-2 rounded' : ''
                            ]"
                        >
                            <div class="flex items-start justify-between">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center">
                                        <!-- Item Number (only for songs) -->
                                        <span v-if="item.type === 'song'" class="text-sm font-medium text-neutral-900">
                                            {{ setlist.items.filter(i => i.type === 'song' && i.order <= item.order).length }}.
                                        </span>

                                        <!-- Break Badge -->
                                        <div v-if="item.type === 'break'"
                                            class="flex items-center px-2 py-1 rounded-md bg-neutral-100 text-neutral-700 mx-2"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
<!--                                            <span class="text-xs font-medium">BREAK</span>-->
                                        </div>

                                        <!-- Item Title -->
                                        <span class="text-sm font-medium text-neutral-900" :class="{ 'ml-2': item.type === 'song' }">
                                            {{ item.type === 'break' ? item.title : item.song.name }}
                                        </span>

                                        <!-- Duration -->
                                        <span class="ml-2 text-xs text-neutral-500">
                                            {{ formatDuration(item.duration_seconds) }}
                                        </span>
                                    </div>

                                    <!-- Notes -->
                                    <div v-if="item.notes" class="mt-2 text-sm text-neutral-600">
                                        {{ item.notes }}
                                    </div>
                                </div>

                                <!-- Actions (only for songs) -->
                                <div v-if="item.type === 'song'" class="ml-4 flex items-center space-x-2">
                                    <a
                                        v-if="item.song.url"
                                        :href="item.song.url"
                                        target="_blank"
                                        class="text-neutral-400 hover:text-neutral-500"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>

                                    <button
                                        v-if="item.type === 'song'"
                                        @click="visitLyricsUrl(item.song)"
                                        class="text-neutral-400 hover:text-neutral-500"
                                    >
                                        <img class="mr-2 h-5 w-auto text-neutral-400"
                                             src="/images/instruments/microphone.svg"
                                             alt="microphone"
                                        />
                                    </button>

                                    <button
                                        v-for="file in item.song.files"
                                        :key="file.id"
                                        @click="handleFileDownload(file, item.song)"
                                        class="text-neutral-400 hover:text-neutral-500"
                                    >
                                        <img v-if="file.instrument"
                                             class="mr-2 h-5 w-auto text-neutral-400"
                                             :src="`/images/instruments/${file.instrument}.svg`"
                                             :alt="file.instrument"
                                        />
                                        <svg v-else
                                             class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </DSCard>
        </div>

        <DSAlertModal
            ref="deleteModal"
            title="Delete Setlist"
            message="Are you sure you want to delete this setlist? This action cannot be undone."
            type="error"
            confirm-text="Delete"
            :show-cancel="true"
            @confirm="handleDelete"
        />
    </AuthenticatedLayout>
</template>

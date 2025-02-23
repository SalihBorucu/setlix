<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard, DSTooltip } from '@/Components/UI'
import visitExternalLink from '@/Utilities/visitExternalLink'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    songs: {
        type: Array,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    }
})

const page = usePage()
const trial = computed(() => page.props.trial || {})

// Trial limit check
const canAddSongs = computed(() => {
    if (trial.value?.isSubscribed) return true
    return props.songs.length < 10
})

const deleteSong = (songId) => {
    if (confirm('Are you sure you want to delete this song? This action cannot be undone.')) {
        router.delete(route('songs.destroy', [props.band.id, songId]))
    }
}

const handleDownload = (file, song) => {
    visitExternalLink(
        route('songs.files.download', [props.band.id, song.id, file.id]),
        true,
        true
    )
}
</script>

<template>
    <Head :title="`${band.name} - Songs`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
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
                        Songs
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        Manage your band's song library
                    </p>
                </div>
                <div class="mt-4 flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3 md:ml-4 md:mt-0">
                    <Link :href="route('bands.show', band.id)">
                        <DSButton variant="secondary" class="w-full sm:w-auto">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Band
                        </DSButton>
                    </Link>
                    <template v-if="isAdmin">
                        <DSTooltip v-if="!canAddSongs">
                            <DSButton variant="primary" class="w-full sm:w-auto" disabled>
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add New Song
                            </DSButton>
                            <template #content>
                                Free trial allows maximum 10 songs. Please subscribe to add more.
                            </template>
                        </DSTooltip>
                        <Link v-else :href="route('songs.create', band.id)">
                            <DSButton variant="primary" class="w-full sm:w-auto">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add New Song
                            </DSButton>
                        </Link>
                        <DSTooltip v-if="!canAddSongs">
                            <DSButton variant="primary" class="w-full sm:w-auto" disabled>
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Add Multiple Songs
                            </DSButton>
                            <template #content>
                                Free trial allows maximum 10 songs. Please subscribe to add more.
                            </template>
                        </DSTooltip>
                        <Link v-else :href="route('songs.bulk-create', band.id)">
                            <DSButton variant="primary" class="w-full sm:w-auto">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Add Multiple Songs
                            </DSButton>
                        </Link>
                    </template>
                </div>
            </div>
        </template>

        <DSCard>
            <div class="p-6">
                <div v-if="songs.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-neutral-900">No songs</h3>
                    <p class="mt-1 text-sm text-neutral-500">Get started by creating your first song.</p>
                    <div class="mt-6">
                        <template v-if="isAdmin">
                            <DSTooltip v-if="!canAddSongs">
                                <DSButton variant="primary" disabled>
                                    Add Your First Song
                                </DSButton>
                                <template #content>
                                    Free trial allows maximum 10 songs. Please subscribe to add more.
                                </template>
                            </DSTooltip>
                            <Link v-else :href="route('songs.create', band.id)">
                                <DSButton variant="primary">
                                    Add Your First Song
                                </DSButton>
                            </Link>
                        </template>
                    </div>
                </div>
                <div v-else>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-neutral-200">
                            <thead class="bg-neutral-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        Duration
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        Resources
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-neutral-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200">
                                <tr v-for="song in songs" :key="song.id" class="hover:bg-neutral-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <Link
                                            :href="route('songs.show', [band.id, song.id])"
                                            class="text-sm font-medium text-neutral-900 hover:text-primary-600"
                                        >
                                            {{ song.name }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center text-sm text-neutral-500">
                                            <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ song.formatted_duration }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            <div
                                                v-for="file in song.files"
                                                :key="file.id"
                                                class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700 cursor-pointer"
                                                @click="handleDownload(file, song)"
                                            >
                                                <div class="inline-flex items-center">
                                                    <svg class="-ml-0.5 mr-1.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ file.type.charAt(0).toUpperCase() + file.type.slice(1) }}
                                                </div>
                                            </div>
                                            <a
                                                v-if="song.url"
                                                :href="song.url"
                                                target="_blank"
                                                class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700 hover:bg-primary-100"
                                            >
                                                <svg class="-ml-0.5 mr-1.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                URL
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="flex justify-end items-center space-x-3">
                                            <Link
                                                :href="route('songs.show', [band.id, song.id])"
                                                class="text-neutral-400 hover:text-neutral-500"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                            <Link
                                                v-if="isAdmin"
                                                :href="route('songs.edit', [band.id, song.id])"
                                                class="text-neutral-400 hover:text-neutral-500"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </Link>
                                            <button
                                                v-if="isAdmin"
                                                @click="deleteSong(song.id)"
                                                class="text-danger-400 hover:text-danger-500"
                                            >
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </DSCard>
    </AuthenticatedLayout>
</template> 
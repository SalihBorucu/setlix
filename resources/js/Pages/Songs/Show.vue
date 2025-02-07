<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard, DSAlert } from '@/Components/UI'
import { ref } from 'vue'

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    song: {
        type: Object,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    }
})

const confirmDelete = () => {
    if (confirm('Are you sure you want to delete this song? This action cannot be undone.')) {
        router.delete(route('songs.destroy', [props.band.id, props.song.id]))
    }
}
</script>

<template>
    <Head :title="`${song.name} - ${band.name}`" />

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
                        {{ song.name }}
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        Added {{ song.formatted_created_at }}
                    </p>
                </div>
                <div v-if="isAdmin" class="mt-4 flex md:ml-4 md:mt-0 space-x-3">
                    <Link :href="route('songs.edit', [band.id, song.id])">
                        <DSButton variant="secondary">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Song
                        </DSButton>
                    </Link>
                    <DSButton
                        variant="danger"
                        class="bg-danger-600 hover:bg-danger-700 focus:ring-danger-500"
                        @click="confirmDelete"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Song
                    </DSButton>
                </div>
            </div>
        </template>

        <div class="grid gap-6 md:grid-cols-2">
            <!-- Song Details -->
            <DSCard>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">Details</h3>
                    </div>
                    <dl class="divide-y divide-neutral-200">
                        <!-- Duration -->
                        <div class="py-4">
                            <dt class="text-sm font-medium text-neutral-500">Duration</dt>
                            <dd class="mt-1 flex items-center text-sm text-neutral-900">
                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ song.formatted_duration }}
                            </dd>
                        </div>

                        <!-- External Link -->
                        <div v-if="song.url" class="py-4">
                            <dt class="text-sm font-medium text-neutral-500">External Link</dt>
                            <dd class="mt-1 flex items-center text-sm">
                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                <a
                                    :href="song.url"
                                    target="_blank"
                                    class="text-primary-600 hover:text-primary-700"
                                >
                                    {{ song.url }}
                                </a>
                            </dd>
                        </div>

                        <!-- Document -->
                        <div v-if="song.document_path" class="py-4">
                            <dt class="text-sm font-medium text-neutral-500">Document</dt>
                            <dd class="mt-1 flex items-center text-sm">
                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <Link
                                    :href="route('songs.document', [band.id, song.id])"
                                    class="text-primary-600 hover:text-primary-700"
                                    download
                                >
                                    Download {{ song.document_type?.toUpperCase() }}
                                </Link>
                            </dd>
                        </div>

                        <!-- Created At -->
                        <div class="py-4">
                            <dt class="text-sm font-medium text-neutral-500">Added</dt>
                            <dd class="mt-1 flex items-center text-sm text-neutral-900">
                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ song.formatted_created_at }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </DSCard>

            <!-- Notes -->
            <DSCard>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">Notes</h3>
                    </div>
                    <div class="prose prose-neutral max-w-none">
                        <p v-if="song.notes" class="whitespace-pre-line">{{ song.notes }}</p>
                        <p v-else class="text-neutral-500 italic">No notes added yet.</p>
                    </div>
                </div>
            </DSCard>

            <!-- Setlists -->
            <DSCard class="md:col-span-2">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">Setlists</h3>
                    </div>
                    <div v-if="song.setlists?.length" class="divide-y divide-neutral-200">
                        <div
                            v-for="setlist in song.setlists"
                            :key="setlist.id"
                            class="flex items-center justify-between py-4"
                        >
                            <div>
                                <Link
                                    :href="route('setlists.show', [band.id, setlist.id])"
                                    class="text-sm font-medium text-neutral-900 hover:text-primary-600"
                                >
                                    {{ setlist.name }}
                                </Link>
                                <p class="mt-1 text-xs text-neutral-500">
                                    {{ setlist.songs_count }} songs · {{ setlist.total_duration }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-neutral-500">
                                    Position {{ setlist.pivot.order }}
                                </span>
                                <Link
                                    :href="route('setlists.show', [band.id, setlist.id])"
                                    class="text-primary-600 hover:text-primary-700"
                                >
                                    <DSButton variant="outline" size="sm">View</DSButton>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-neutral-900">No setlists</h3>
                        <p class="mt-1 text-sm text-neutral-500">This song isn't part of any setlists yet.</p>
                        <div class="mt-6">
                            <Link :href="route('setlists.create', { band: band.id })">
                                <DSButton variant="primary" size="sm">
                                    Create New Setlist
                                </DSButton>
                            </Link>
                        </div>
                    </div>
                </div>
            </DSCard>
        </div>
    </AuthenticatedLayout>
</template> 
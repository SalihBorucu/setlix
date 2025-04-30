<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard, DSAlertModal, DSTooltip } from '@/Components/UI'
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    setlists: {
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
const canAddSetlists = computed(() => {
    if (trial.value?.isSubscribed) return true
    return props.setlists.length < 3
})

const deleteModal = ref(null)
const setlistToDelete = ref(null)

const confirmDelete = (setlist) => {
    setlistToDelete.value = setlist
    deleteModal.value?.open()
}

const handleDelete = () => {
    if (setlistToDelete.value) {
        router.delete(route('setlists.destroy', [props.band.id, setlistToDelete.value.id]))
    }
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
</script>

<template>
    <Head :title="`${band.name} - Setlists`" />

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
                        Setlists
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        Manage your band's setlists
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
                        <DSTooltip v-if="!canAddSetlists">
                            <DSButton variant="primary" class="w-full sm:w-auto" disabled>
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create New Setlist
                            </DSButton>
                            <template #content>
                                Free trial allows maximum 3 setlists. Please subscribe to add more.
                            </template>
                        </DSTooltip>
                        <Link v-else :href="route('setlists.create', band.id)">
                            <DSButton variant="primary" class="w-full sm:w-auto">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create New Setlist
                            </DSButton>
                        </Link>
                    </template>
                </div>
            </div>
        </template>

        <DSCard>
            <div class="p-6">
                <div v-if="setlists.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-neutral-900">No setlists</h3>
                    <p class="mt-1 text-sm text-neutral-500">Get started by creating your first setlist.</p>
                    <div class="mt-6">
                        <template v-if="isAdmin">
                            <DSTooltip v-if="!canAddSetlists">
                                <DSButton variant="primary" disabled>
                                    Create Your First Setlist
                                </DSButton>
                                <template #content>
                                    Free trial allows maximum 3 setlists. Please subscribe to add more.
                                </template>
                            </DSTooltip>
                            <Link v-else :href="route('setlists.create', band.id)">
                                <DSButton variant="primary">
                                    Create Your First Setlist
                                </DSButton>
                            </Link>
                        </template>
                    </div>
                </div>
                <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <DSCard
                        v-for="setlist in setlists"
                        :key="setlist.id"
                        class="hover:shadow-lg transition-shadow duration-200"
                    >
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="min-w-0 flex-1">
                                    <Link
                                        :href="route('setlists.show', [band.id, setlist.id])"
                                        class="block text-lg font-semibold text-neutral-900 hover:text-primary-600"
                                    >
                                        {{ setlist.name }}
                                    </Link>
                                    <p v-if="setlist.description" class="mt-1 text-sm text-neutral-500 line-clamp-2">
                                        {{ setlist.description }}
                                    </p>
                                </div>
                                <div v-if="isAdmin" class="ml-4 flex items-center space-x-2">
                                    <Link
                                        :href="route('setlists.edit', [band.id, setlist.id])"
                                        class="text-neutral-400 hover:text-neutral-500"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </Link>
                                    <button
                                        @click="confirmDelete(setlist)"
                                        class="text-danger-400 hover:text-danger-500"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between border-t border-neutral-200 pt-4">
                                <div class="flex items-center space-x-4 text-sm text-neutral-500">
                                    <div class="flex items-center">
                                        <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                        </svg>
                                        {{ setlist.items?.length || 0 }} songs
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ formatDuration(setlist.total_duration) }}
                                    </div>
                                </div>
                                <div>
                                    <Link
                                        :href="route('setlists.show', [band.id, setlist.id])"
                                        class="text-primary-600 hover:text-primary-700"
                                    >
                                        <DSButton variant="outline" size="sm">
                                            View
                                        </DSButton>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </DSCard>
                </div>
            </div>
        </DSCard>

        <DSAlertModal
            ref="deleteModal"
            :title="`Delete ${setlistToDelete?.name || 'Setlist'}`"
            message="Are you sure you want to delete this setlist? This action cannot be undone."
            type="error"
            confirm-text="Delete"
            :show-cancel="true"
            @confirm="handleDelete"
        />
    </AuthenticatedLayout>
</template>

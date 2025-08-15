<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard, DSTooltip } from '@/Components/UI'
import ConfirmModal from '@/Components/UI/ConfirmModal.vue'
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    },
    currentUserRole: {
        type: String,
        required: true
    }
})

const showDeleteModal = ref(false)
const showLeaveModal = ref(false)

const page = usePage()
const trial = computed(() => page.props.trial || {})

// Trial limit checks
const canAddSongs = computed(() => {
    if (trial.value?.isSubscribed) return true
    return (props.band.songs_count || 0) < 10
})

const canAddSetlists = computed(() => {
    if (trial.value?.isSubscribed) return true
    return (props.band.setlists_count || 0) < 3
})

const deleteBand = () => {
    router.delete(route('bands.destroy', props.band.id))
}

const leaveBand = () => {
    router.delete(route('bands.leave', props.band.id))
}
</script>

<template>
    <Head :title="band.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        {{ band.name }}
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        {{ band.members_count }} members
                    </p>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0 space-x-3">
                    <!-- Leave Band Button (for non-admin members) -->
                    <DSButton
                        v-if="!isAdmin && currentUserRole === 'member'"
                        variant="danger"
                        @click="showLeaveModal = true"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Leave Band
                    </DSButton>

                    <!-- Admin Actions -->
                    <template v-if="isAdmin">
                        <Link :href="route('bands.edit', band.id)">
                            <DSButton variant="secondary">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Edit Band
                            </DSButton>
                        </Link>
                        <DSButton
                            variant="danger"
                            @click="showDeleteModal = true"
                        >
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Band
                        </DSButton>
                    </template>
                </div>
            </div>
        </template>

        <div class="grid gap-6 md:grid-cols-2">
            <!-- Band Info -->
            <DSCard>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">About</h3>
                    </div>
                    <div class="space-y-4">
                        <!-- Cover Image -->
                        <div v-if="band.cover_image" class="aspect-video w-full overflow-hidden rounded-lg">
                            <img
                                :src="band.cover_image"
                                :alt="band.name"
                                class="h-full w-full object-cover"
                            />
                        </div>

                        <!-- Description -->
                        <p class="text-neutral-600">
                            {{ band.description || 'No description provided.' }}
                        </p>
                    </div>
                </div>
            </DSCard>

            <!-- Members List -->
            <DSCard>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">Members</h3>
                        <Link v-if="isAdmin" :href="route('bands.members.index', band.id)">
                            <DSButton variant="secondary" size="sm">
                                Manage Members
                            </DSButton>
                        </Link>
                    </div>
                    <div class="divide-y divide-neutral-200">
                        <div
                            v-for="member in band.members"
                            :key="member.id"
                            class="flex items-center justify-between py-3"
                        >
                            <div class="flex items-center">
                                <img
                                    :src="member.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(member.name)}`"
                                    :alt="member.name"
                                    class="h-8 w-8 rounded-full"
                                />
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-neutral-900">{{ member.name }}</p>
                                    <p class="text-xs text-neutral-500">{{ member.email }}</p>
                                </div>
                            </div>
                            <span
                                v-if="member.pivot.role === 'admin'"
                                class="inline-flex items-center rounded-full bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700"
                            >
                                Admin
                            </span>
                        </div>
                    </div>
                </div>
            </DSCard>

            <!-- Quick Actions -->
            <DSCard class="md:col-span-2">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-neutral-900">Quick Actions</h3>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Songs -->
                        <div class="relative group">
                            <div class="h-full rounded-lg border border-neutral-200 p-6">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-base font-medium text-neutral-900">Songs</h4>
                                    <span class="text-2xl font-semibold text-neutral-900">{{ band.songs_count || 0 }}</span>
                                </div>
                                <div class="mt-4 flex justify-between items-center">
                                    <Link :href="route('songs.index', { band: band.id })">
                                        <DSButton variant="outline" size="sm">
                                            View All
                                        </DSButton>
                                    </Link>
                                    <template v-if="isAdmin">
<!--                                        <DSTooltip v-if="!canAddSongs">-->
<!--                                            <DSButton variant="primary" size="sm" disabled>-->
<!--                                                Add New Song-->
<!--                                            </DSButton>-->
<!--                                            <template #content>-->
<!--                                                Free trial allows maximum 10 songs. Please subscribe to add more.-->
<!--                                            </template>-->
<!--                                        </DSTooltip>-->
                                        <Link :href="route('songs.create', { band: band.id })">
                                            <DSButton variant="primary" size="sm">
                                                Add New Song
                                            </DSButton>
                                        </Link>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Setlists -->
                        <div class="relative group">
                            <div class="h-full rounded-lg border border-neutral-200 p-6">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-base font-medium text-neutral-900">Setlists</h4>
                                    <span class="text-2xl font-semibold text-neutral-900">{{ band.setlists_count || 0 }}</span>
                                </div>
                                <div class="mt-4 flex justify-between items-center">
                                    <Link :href="route('setlists.index', { band: band.id })">
                                        <DSButton variant="outline" size="sm">
                                            View All
                                        </DSButton>
                                    </Link>
                                    <template v-if="isAdmin">
                                        <DSTooltip v-if="!canAddSetlists">
                                            <DSButton variant="primary" size="sm" disabled>
                                                Create New Setlist
                                            </DSButton>
                                            <template #content>
                                                Free trial allows maximum 3 setlists. Please subscribe to add more.
                                            </template>
                                        </DSTooltip>
                                        <Link v-else :href="route('setlists.create', { band: band.id })">
                                            <DSButton variant="primary" size="sm">
                                                Create New Setlist
                                            </DSButton>
                                        </Link>
                                    </template>
                                </div>
                            </div>
                        </div>

<!--                        &lt;!&ndash; Upcoming Shows &ndash;&gt;-->
<!--                        <div class="h-full rounded-lg border border-neutral-200 p-6">-->
<!--                            <div class="flex items-center justify-between">-->
<!--                                <h4 class="text-base font-medium text-neutral-900">Upcoming Shows</h4>-->
<!--                                <span class="text-2xl font-semibold text-neutral-900">0</span>-->
<!--                            </div>-->
<!--                            <p class="mt-1 text-sm text-neutral-500">Coming soon</p>-->
<!--                        </div>-->

<!--                        &lt;!&ndash; Statistics &ndash;&gt;-->
<!--                        <div class="h-full rounded-lg border border-neutral-200 p-6">-->
<!--                            <div class="flex items-center justify-between">-->
<!--                                <h4 class="text-base font-medium text-neutral-900">Statistics</h4>-->
<!--                                <span class="text-2xl font-semibold text-neutral-900">-</span>-->
<!--                            </div>-->
<!--                            <p class="mt-1 text-sm text-neutral-500">Coming soon</p>-->
<!--                        </div>-->
                    </div>
                </div>
            </DSCard>
        </div>

        <!-- Delete Band Modal -->
        <ConfirmModal
            v-model="showDeleteModal"
            title="Delete Band"
            description="Are you sure you want to delete this band? This action cannot be undone and all associated data will be permanently deleted."
            confirm-text="Delete Band"
            @confirm="deleteBand"
        />

        <!-- Leave Band Modal -->
        <ConfirmModal
            v-model="showLeaveModal"
            title="Leave Band"
            description="Are you sure you want to leave this band? You will lose access to all band content and will need to be invited back to rejoin."
            confirm-text="Leave Band"
            @confirm="leaveBand"
        />
    </AuthenticatedLayout>
</template>

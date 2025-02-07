<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard } from '@/Components/UI'
import { ref } from 'vue'

const props = defineProps({
    band: {
        type: Object,
        required: true
    },
    isAdmin: {
        type: Boolean,
        required: true
    }
})

const deleteBand = () => {
    if (confirm('Are you sure you want to delete this band? This action cannot be undone.')) {
        router.delete(route('bands.destroy', props.band.id))
    }
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
                <div v-if="isAdmin" class="mt-4 flex md:ml-4 md:mt-0 space-x-3">
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
                        @click="deleteBand"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Band
                    </DSButton>
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
                        <DSButton v-if="isAdmin" variant="secondary" size="sm">
                            Manage Members
                        </DSButton>
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
                                    <Link 
                                        v-if="isAdmin"
                                        :href="route('songs.create', { band: band.id })"
                                    >
                                        <DSButton variant="primary" size="sm">
                                            Add New Song
                                        </DSButton>
                                    </Link>
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
                                    <Link 
                                        v-if="isAdmin"
                                        :href="route('setlists.create', { band: band.id })"
                                    >
                                        <DSButton variant="primary" size="sm">
                                            Create New Setlist
                                        </DSButton>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Shows -->
                        <div class="h-full rounded-lg border border-neutral-200 p-6">
                            <div class="flex items-center justify-between">
                                <h4 class="text-base font-medium text-neutral-900">Upcoming Shows</h4>
                                <span class="text-2xl font-semibold text-neutral-900">0</span>
                            </div>
                            <p class="mt-1 text-sm text-neutral-500">Coming soon</p>
                        </div>

                        <!-- Statistics -->
                        <div class="h-full rounded-lg border border-neutral-200 p-6">
                            <div class="flex items-center justify-between">
                                <h4 class="text-base font-medium text-neutral-900">Statistics</h4>
                                <span class="text-2xl font-semibold text-neutral-900">-</span>
                            </div>
                            <p class="mt-1 text-sm text-neutral-500">Coming soon</p>
                        </div>
                    </div>
                </div>
            </DSCard>
        </div>
    </AuthenticatedLayout>
</template> 
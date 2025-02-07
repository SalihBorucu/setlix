<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSCard } from '@/Components/UI'

const props = defineProps({
    bands: {
        type: Object,
        required: true,
        default: () => ({
            data: [],
            meta: {
                current_page: 1,
                last_page: 1,
                total: 0
            }
        })
    }
})

// Computed property to safely check if we have bands
const hasBands = () => {
    return props.bands?.data?.length > 0
}

// Computed properties for pagination
const showPagination = () => {
    return props.bands?.meta?.last_page > 1
}

const currentPage = () => {
    return props.bands?.meta?.current_page || 1
}

const lastPage = () => {
    return props.bands?.meta?.last_page || 1
}

const total = () => {
    return props.bands?.meta?.total || 0
}

const fromItem = () => {
    return ((currentPage() - 1) * 10) + 1
}

const toItem = () => {
    return Math.min(currentPage() * 10, total())
}
</script>

<template>
    <Head title="Your Bands" />

    <AuthenticatedLayout>
        <template #header>
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Your Bands
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        Manage your bands and collaborations
                    </p>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <Link :href="route('bands.create')">
                        <DSButton variant="primary">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create New Band
                        </DSButton>
                    </Link>
                </div>
            </div>
        </template>

        <div v-if="!hasBands()" class="text-center">
            <DSCard class="px-6 py-12">
                <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2M12 3a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-neutral-900">No bands yet</h3>
                <p class="mt-1 text-sm text-neutral-500">Get started by creating your first band.</p>
                <div class="mt-6">
                    <Link :href="route('bands.create')">
                        <DSButton variant="primary" size="lg">
                            Create Your First Band
                        </DSButton>
                    </Link>
                </div>
            </DSCard>
        </div>

        <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <DSCard v-for="band in bands.data" :key="band.id" class="group relative">
                <!-- Band Card Header -->
                <div class="relative h-40 bg-primary-600">
                    <img 
                        v-if="band.cover_image"
                        :src="band.cover_image"
                        :alt="band.name"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="h-full w-full bg-gradient-to-br from-primary-500 to-primary-700">
                        <div class="flex h-full items-center justify-center">
                            <span class="text-4xl font-bold text-white opacity-20">
                                {{ band.name.charAt(0) }}
                            </span>
                        </div>
                    </div>
                    <div class="absolute inset-0 bg-black/20"></div>
                </div>

                <!-- Band Info -->
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <Link 
                                :href="route('bands.show', band.id)"
                                class="text-lg font-semibold text-neutral-900 hover:text-primary-600"
                            >
                                {{ band.name }}
                            </Link>
                            <p class="mt-1 text-sm text-neutral-500">
                                {{ band.members_count }} members
                            </p>
                        </div>
                        <div v-if="band.role === 'admin'" class="ml-2">
                            <span class="inline-flex items-center rounded-full bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700">
                                Admin
                            </span>
                        </div>
                    </div>

                    <p v-if="band.description" class="mt-3 text-sm text-neutral-600 line-clamp-2">
                        {{ band.description }}
                    </p>

                    <!-- Quick Stats -->
                    <div class="mt-4 grid grid-cols-2 gap-4 border-t border-neutral-200 pt-4">
                        <div>
                            <p class="text-sm font-medium text-neutral-500">Songs</p>
                            <p class="mt-1 text-lg font-semibold text-neutral-900">{{ band.songs_count || 0 }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-neutral-500">Setlists</p>
                            <p class="mt-1 text-lg font-semibold text-neutral-900">{{ band.setlists_count || 0 }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex space-x-3">
                        <Link 
                            :href="route('songs.index', { band: band.id })"
                            class="flex-1"
                        >
                            <DSButton variant="outline" class="w-full" size="sm">
                                View Songs
                            </DSButton>
                        </Link>
                        <Link 
                            :href="route('setlists.index', { band: band.id })"
                            class="flex-1"
                        >
                            <DSButton variant="outline" class="w-full" size="sm">
                                View Setlists
                            </DSButton>
                        </Link>
                    </div>
                </div>

                <!-- Admin Actions -->
                <div v-if="band.role === 'admin'" class="absolute right-4 top-4 opacity-0 transition-opacity group-hover:opacity-100">
                    <Link :href="route('bands.edit', band.id)">
                        <DSButton variant="secondary" size="sm">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit
                        </DSButton>
                    </Link>
                </div>
            </DSCard>
        </div>

        <!-- Pagination -->
        <div v-if="showPagination()" class="mt-6 flex items-center justify-between border-t border-neutral-200 px-4 py-3 sm:px-6">
            <div class="flex flex-1 justify-between sm:hidden">
                <Link
                    v-if="currentPage() > 1"
                    :href="route('bands.index', { page: currentPage() - 1 })"
                    class="relative inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                >
                    Previous
                </Link>
                <Link
                    v-if="currentPage() < lastPage()"
                    :href="route('bands.index', { page: currentPage() + 1 })"
                    class="relative ml-3 inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-50"
                >
                    Next
                </Link>
            </div>
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-neutral-700">
                        Showing
                        <span class="font-medium">{{ fromItem() }}</span>
                        to
                        <span class="font-medium">{{ toItem() }}</span>
                        of
                        <span class="font-medium">{{ total() }}</span>
                        results
                    </p>
                </div>
                <div>
                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        <Link
                            v-if="currentPage() > 1"
                            :href="route('bands.index', { page: currentPage() - 1 })"
                            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-neutral-400 ring-1 ring-inset ring-neutral-300 hover:bg-neutral-50"
                        >
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                        <Link
                            v-if="currentPage() < lastPage()"
                            :href="route('bands.index', { page: currentPage() + 1 })"
                            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-neutral-400 ring-1 ring-inset ring-neutral-300 hover:bg-neutral-50"
                        >
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template> 
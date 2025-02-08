<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSCard, DSButton } from '@/Components/UI'

const props = defineProps({
    bands: {
        type: Array,
        default: () => []
    }
})

// Add debug output
console.log('Bands data:', props.bands)

// Helper function to check admin status
const hasAdminRole = (band) => {
    // Check both is_admin and roles/pivot data since Laravel might send it in different ways
    return band.is_admin || 
           (band.pivot && band.pivot.role === 'admin') || 
           (band.roles && band.roles.includes('admin'))
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        My Bands
                    </h2>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <Link :href="route('bands.create')">
                        <DSButton variant="primary">
                            Create New Band
                        </DSButton>
                    </Link>
                </div>
            </div>
        </template>

        <!-- No Bands State -->
        <div v-if="bands.length === 0" class="text-center">
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

        <template v-else>
            <!-- Bands Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link 
                    v-for="band in bands" 
                    :key="band.id" 
                    :href="route('bands.show', band.id)"
                    class="group"
                >
                    <DSCard class="h-full transition-shadow hover:shadow-lg">
                        <div class="aspect-w-16 aspect-h-9 relative overflow-hidden rounded-t-lg">
                            <img
                                :src="band.cover_image_thumbnail_path || 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1740&q=80'"
                                class="object-cover transition-transform group-hover:scale-105"
                                :alt="band.name"
                            />
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-neutral-900 group-hover:text-primary-600">
                                    {{ band.name }}
                                </h3>
                                <div v-if="hasAdminRole(band)" class="flex items-center text-primary-600">
                                    <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    <span class="text-sm font-medium">Admin</span>
                                </div>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-neutral-500">
                                <svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-neutral-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655zM16.44 15.98a4.97 4.97 0 002.07-.654.78.78 0 00.357-.442 3 3 0 00-4.308-3.517 6.484 6.484 0 011.907 3.96 2.32 2.32 0 01-.026.654zM18 8a2 2 0 11-4 0 2 2 0 014 0zM5.304 16.19a.844.844 0 01-.277-.71 5 5 0 019.947 0 .843.843 0 01-.277.71A6.975 6.975 0 0110 18a6.974 6.974 0 01-4.696-1.81z" />
                                </svg>
                                {{ band.members_count }} members
                            </div>
                            <div class="mt-4 flex space-x-3">
                                <Link 
                                    :href="route('songs.index', { band: band.id })"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-700"
                                >
                                    View Songs
                                </Link>
                                <Link 
                                    :href="route('setlists.index', { band: band.id })"
                                    class="text-sm font-medium text-primary-600 hover:text-primary-700"
                                >
                                    View Setlists
                                </Link>
                            </div>
                        </div>
                    </DSCard>
                </Link>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

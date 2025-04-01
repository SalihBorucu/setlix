<script setup>
import { Link } from '@inertiajs/vue3'
import PublicAccessToggle from './PublicAccessToggle.vue'

defineProps({
    band: {
        type: Object,
        required: true
    },
    setlist: {
        type: Object,
        required: false,
        default: null
    }
})
</script>

<template>
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1 test">
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
                <Link
                    :href="route('setlists.index', band.id)"
                    class="text-sm font-medium text-primary-600 hover:text-primary-700"
                >
                    Setlists
                </Link>
            </div>
            <h2 class="mt-1 text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                {{ setlist ? setlist.name : 'Create New Setlist' }}
            </h2>
            <p v-if="setlist?.description" class="mt-1 text-sm text-neutral-500">
                {{ setlist.description }}
            </p>
        </div>
        <div class="mt-4 flex md:ml-4 md:mt-0">
            <PublicAccessToggle v-if="setlist" :band="band" :setlist="setlist" />
        </div>
    </div>
</template>

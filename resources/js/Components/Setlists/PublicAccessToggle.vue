<template>
    <div class="flex items-center space-x-4">
        <div v-if="setlist.is_public" class="flex items-center space-x-2">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                Shared with Client
            </span>
            <button
                @click="makePrivate"
                class="text-sm text-gray-600 hover:text-gray-900"
            >
                Stop Sharing
            </button>
        </div>
        <div v-else>
            <button
                @click="makePublic"
                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Share with Client
            </button>
        </div>

        <ShareSetlistModal
            :show="isShareModalOpen"
            :setlist="setlist"
            @close="closeShareModal"
        />
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import ShareSetlistModal from './ShareSetlistModal.vue'

const props = defineProps({
    setlist: Object,
    band: Object
})

const isShareModalOpen = ref(false)

const makePublic = () => {
    axios.post(route('setlists.make-public', [props.band.id, props.setlist.id]))
        .then(response => {
            // Update the setlist with the new public_slug
            props.setlist.is_public = true
            props.setlist.public_slug = response.data.setlist.public_slug
            isShareModalOpen.value = true
        })
}

const makePrivate = () => {
    axios.post(route('setlists.make-private', [props.band.id, props.setlist.id]))
        .then(response => {
            // Update the setlist with the new state
            props.setlist.is_public = false
            props.setlist.public_slug = null
        })
}

const closeShareModal = () => {
    isShareModalOpen.value = false
}
</script> 
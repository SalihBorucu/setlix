<template>
    <Modal
        :show="show"
        @close="$emit('close')"
        title="Share Setlist with Client"
        max-width="md"
    >
        <div class="space-y-4">
            <p class="text-sm text-gray-500">
                Share this link with your client to let them customize the setlist:
            </p>
            <div class="flex rounded-md shadow-sm">
                <input
                    type="text"
                    :value="shareUrl"
                    readonly
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                >
                <button
                    @click="copyToClipboard"
                    class="ml-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Copy
                </button>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end">
                <button
                    type="button"
                    class="inline-flex justify-center rounded-md border border-transparent bg-indigo-100 px-4 py-2 text-sm font-medium text-indigo-900 hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2"
                    @click="$emit('close')"
                >
                    Close
                </button>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { computed } from 'vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    setlist: {
        type: Object,
        required: true
    }
})

defineEmits(['close'])

const shareUrl = computed(() => {
    return route('public.setlist.show', props.setlist.public_slug)
})

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(shareUrl.value)
        // Show success toast
        const toast = document.createElement('div')
        toast.className = 'fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded'
        toast.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Link copied to clipboard!</span>
            </div>
        `
        document.body.appendChild(toast)
        setTimeout(() => {
            toast.remove()
        }, 3000)
    } catch (err) {
        console.error('Failed to copy text: ', err)
    }
}
</script> 
<template>
    <div class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="max-w-md w-full mx-auto p-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Thank You!</h2>
                    <p class="text-gray-600">Your setlist preferences have been submitted successfully!</p>
                    <div class="mt-4 space-y-3 text-gray-600">
                        <p>We'll notify the band about your song selections and any special notes you've added.</p>
                        <p class="text-sm text-gray-500">They will review your preferences and get in touch with you through the email you provided.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    message: String
})
</script> 
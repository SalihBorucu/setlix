<template>
    <div v-if="hasMessage" class="fixed top-4 right-4 z-50 max-w-sm">
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <DSAlert :type="type" class="shadow-xl rounded-lg border bg-white p-4">
                <div class="flex items-center gap-3">
                    <!-- Success Icon -->
                    <div v-if="type === 'success'" class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <!-- Error Icon -->
                    <div v-if="type === 'error'" class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <!-- Info Icon -->
                    <div v-if="type === 'info'" class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1 text-sm font-medium">
                        {{ message }}
                    </div>
                </div>
            </DSAlert>
        </transition>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { DSAlert } from '@/Components/UI';

const page = usePage();

const message = computed(() => {
    return page.props.flash.success || 
           page.props.flash.error || 
           page.props.flash.info || 
           page.props.flash.message;
});

const type = computed(() => {
    if (page.props.flash.success) return 'success';
    if (page.props.flash.error) return 'error';
    if (page.props.flash.info) return 'info';
    return 'info';
});

const hasMessage = computed(() => {
    return !!message.value;
});
</script> 
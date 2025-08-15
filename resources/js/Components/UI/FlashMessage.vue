<template>
    <div v-if="isVisible" class="fixed top-4 right-4 z-50 w-full max-w-sm">
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" :class="[typeClasses.bg, 'relative rounded-lg shadow-lg']">
                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <component :is="typeClasses.icon" :class="['h-6 w-6', typeClasses.iconColor]" aria-hidden="true" />
                        </div>

                        <div class="w-0 flex-1 pt-0.5">
                            <p :class="[typeClasses.titleColor, 'text-sm font-bold']">
                                {{ title }}
                            </p>
                            <p :class="[typeClasses.textColor, 'mt-1 text-sm']">
                                {{ message }}
                            </p>
                        </div>

                        <div class="ml-4 flex flex-shrink-0">
                            <button
                                type="button"
                                @click="dismiss"
                                :class="[typeClasses.button, 'inline-flex rounded-md p-1 text-gray-500 hover:bg-gray-200/50 focus:outline-none focus:ring-2 focus:ring-offset-2']"
                            >
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div :class="[typeClasses.progressBg, 'absolute bottom-0 left-0 h-1 rounded-bl-lg']" :style="{ width: progressWidth + '%' }"></div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { computed, ref, watch, onUnmounted, markRaw } from 'vue';
import { usePage } from '@inertiajs/vue3';

// --- Icons ---
// Using markRaw to prevent Vue from making these reactive, which is a small performance gain.
const IconSuccess = markRaw({
    template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
});
const IconError = markRaw({
    template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>`,
});
const IconInfo = markRaw({
    template: `<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>`,
});


// --- Component State ---
const page = usePage();
const show = ref(false);
const isVisible = ref(false);
const progressWidth = ref(100);
let timeout = null;
let interval = null;

const DURATION = 7000; // 7 seconds

// --- Computed Properties ---
const flash = computed(() => page.props.flash);

const type = computed(() => {
    if (flash.value.success) return 'success';
    if (flash.value.error) return 'error';
    if (flash.value.info) return 'info';
    return null; // No message type
});

const title = computed(() => {
    switch (type.value) {
        case 'success':
            return 'Success!';
        case 'error':
            return 'An error occurred';
        case 'info':
            return 'Heads up!';
        default:
            return 'Notification';
    }
});

const message = computed(() => {
    isVisible.value = !!(page.props.flash.success ||
        page.props.flash.error ||
        page.props.flash.info ||
        page.props.flash.message);

    return page.props.flash.success ||
        page.props.flash.error ||
        page.props.flash.info ||
        page.props.flash.message;
});

const typeClasses = computed(() => {
    switch (type.value) {
        case 'success':
            return {
                bg: 'bg-green-50',
                icon: IconSuccess,
                iconColor: 'text-green-500',
                titleColor: 'text-green-900',
                textColor: 'text-green-800',
                button: 'focus:ring-green-600 focus:ring-offset-green-50',
                progressBg: 'bg-green-400'
            };
        case 'error':
            return {
                bg: 'bg-red-50',
                icon: IconError,
                iconColor: 'text-red-500',
                titleColor: 'text-red-900',
                textColor: 'text-red-800',
                button: 'focus:ring-red-600 focus:ring-offset-red-50',
                progressBg: 'bg-red-400'
            };
        default: // 'info'
            return {
                bg: 'bg-blue-50',
                icon: IconInfo,
                iconColor: 'text-blue-500',
                titleColor: 'text-blue-900',
                textColor: 'text-blue-800',
                button: 'focus:ring-blue-600 focus:ring-offset-blue-50',
                progressBg: 'bg-blue-400'
            };
    }
});

// --- Functions ---
const dismiss = () => {
    show.value = false;
};

const clearTimers = () => {
    if (timeout) clearTimeout(timeout);
    if (interval) clearInterval(interval);
    timeout = null;
    interval = null;
};

// --- Watchers ---
watch(message, (newMessage) => {
    if (newMessage) {
        isVisible.value = true;
        show.value = true;
        progressWidth.value = 100;
        clearTimers();

        // Start progress bar interval
        const startTime = Date.now();
        interval = setInterval(() => {
            const elapsedTime = Date.now() - startTime;
            progressWidth.value = 100 - (elapsedTime / DURATION) * 100;
        }, 100);

        // Set auto-dismiss timeout
        timeout = setTimeout(() => {
            dismiss();
        }, DURATION);
    } else {
        isVisible.value = false;
        show.value = false;
        clearTimers();
    }
}, {immediate: true});

// Ensure timers are cleaned up when the component is unmounted.
onUnmounted(() => {
    clearTimers();
});
</script>

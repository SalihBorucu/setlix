<script setup>
defineProps({
    bgColor: {
        type: String,
        default: 'bg-white'
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

</script>

<template>
    <div class="relative rounded-lg shadow-sm border border-neutral-200 overflow-hidden" :class="bgColor">
        <span v-if="disabled" class="absolute top-0 left-0 w-full h-full bg-gray-50 opacity-50 cursor-not-allowed"></span>
        <slot></slot>
    </div>
</template>

<script setup>
defineProps({
    bgColor: {
        type: String,
        default: 'bg-white'
    }
});

</script>

<template>
    <div class="rounded-lg shadow-sm border border-neutral-200 overflow-hidden" :class="bgColor">
        <slot></slot>
    </div>
</template>

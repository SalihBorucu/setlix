<script setup>
defineProps({
    type: {
        type: String,
        default: 'info',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    }
})
</script>

<template>
    <div :class="[
        'rounded-lg p-4 border',
        {
            'bg-green-50 border-green-200 text-green-700': type === 'success',
            'bg-red-50 border-red-200 text-red-700': type === 'error',
            'bg-amber-50 border-amber-200 text-amber-700': type === 'warning',
            'bg-primary-50 border-primary-200 text-primary-700': type === 'info'
        }
    ]">
        <slot></slot>
    </div>
</template>

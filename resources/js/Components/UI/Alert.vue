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
            'bg-success-50 border-success-200 text-success-700': type === 'success',
            'bg-error-50 border-error-200 text-error-700': type === 'error',
            'bg-warning-50 border-warning-200 text-warning-700': type === 'warning',
            'bg-primary-50 border-primary-200 text-primary-700': type === 'info'
        }
    ]">
        <slot></slot>
    </div>
</template> 
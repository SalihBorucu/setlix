<script setup>
defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (value) => ['primary', 'secondary', 'outline', 'ghost', 'danger'].includes(value)
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <button
        :class="[
            'inline-flex items-center justify-center rounded-lg transition-colors duration-200 font-medium',
            // Variants
            {
                'bg-primary-500 text-white hover:bg-primary-600 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2': variant === 'primary',
                'bg-secondary-500 text-white hover:bg-secondary-600 focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2': variant === 'secondary',
                'bg-danger-600 text-white hover:bg-danger-700 focus:ring-2 focus:ring-danger-500 focus:ring-offset-2': variant === 'danger',
                'border-2 border-primary-500 text-primary-500 hover:bg-primary-50': variant === 'outline',
                'text-primary-500 hover:bg-primary-50': variant === 'ghost'
            },
            // Sizes - Made more compact for mobile
            {
                'px-2 py-1 text-xs': size === 'sm',
                'px-3 py-1.5 text-sm sm:px-4 sm:py-2 sm:text-base': size === 'md',
                'px-4 py-2 text-base sm:px-6 sm:py-3 sm:text-lg': size === 'lg'
            },
            // States
            {
                'opacity-50 cursor-not-allowed': disabled
            }
        ]"
        :disabled="disabled"
    >
        <slot></slot>
    </button>
</template>

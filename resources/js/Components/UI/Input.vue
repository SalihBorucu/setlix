<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'text'
    },
    modelValue: {
        type: [String, Number],
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: ''
    }
})

defineEmits(['update:modelValue'])
</script>

<template>
    <div class="space-y-1">
        <label v-if="label" class="block text-sm font-medium text-neutral-700">
            {{ label }}
        </label>
        <input
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :class="[
                'block w-full rounded-lg border-neutral-300 shadow-sm transition-colors duration-200',
                'focus:border-primary-500 focus:ring-primary-500',
                { 'border-error-500 focus:border-error-500 focus:ring-error-500': error }
            ]"
        />
        <p v-if="error" class="text-sm text-error-500">{{ error }}</p>
    </div>
</template> 
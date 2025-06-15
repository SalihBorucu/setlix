<script setup>
import { ref, watch, computed } from 'vue'
import { DSInput } from '@/Components/UI'

const props = defineProps({
    modelValue: {
        type: [String, Number],
        required: true,
        default: ''
    },
    label: {
        type: String,
        default: null
    },
    error: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    placeholder: {
        type: String,
        default: null
    },
    includeHours: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue', 'update:seconds'])

// Internal display value
const displayValue = ref('')

// Computed placeholder based on format
const computedPlaceholder = computed(() => {
    if (props.placeholder) return props.placeholder
    return props.includeHours ? '00:00:00' : '00:00'
})

// Computed max length based on format
const computedMaxLength = computed(() => {
    return props.includeHours ? 8 : 5
})

// Convert seconds to display format (MM:SS or HH:MM:SS)
const secondsToDisplay = (seconds) => {
    if (!seconds) return ''

    if (props.includeHours) {
        const hours = Math.floor(seconds / 3600)
        const minutes = Math.floor((seconds % 3600) / 60)
        const remainingSeconds = seconds % 60
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`
    } else {
        const minutes = Math.floor(seconds / 60)
        const remainingSeconds = seconds % 60
        return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`
    }
}

// Convert display format to seconds
const displayToSeconds = (value) => {
    if (props.includeHours) {
        const [hours, minutes, seconds] = value.split(':').map(Number)
        return (hours * 3600) + (minutes * 60) + seconds
    } else {
        const [minutes, seconds] = value.split(':').map(Number)
        return (minutes * 60) + seconds
    }
}

// Initialize display value from props
watch(() => props.modelValue, (newValue) => {
    if (typeof newValue === 'number') {
        displayValue.value = secondsToDisplay(newValue)
    } else {
        displayValue.value = newValue
    }
}, { immediate: true })

// Handle input changes
const handleInput = (value) => {
    // If empty, reset
    if (!value) {
        displayValue.value = ''
        emit('update:modelValue', '')
        return
    }

    // Keep only the last digits entered (4 for MM:SS, 6 for HH:MM:SS)
    const maxDigits = props.includeHours ? 6 : 4
    const digits = value.replace(/[^0-9]/g, '').slice(-maxDigits)

    if (!digits) {
        displayValue.value = ''
        emit('update:modelValue', '')
        return
    }

    let formattedValue = ''

    if (props.includeHours) {
        // Format as HH:MM:SS
        if (digits.length >= 1) {
            const hours = parseInt(digits.slice(0, -4)) || 0
            const minutes = parseInt(digits.slice(-4, -2)) || 0
            const seconds = parseInt(digits.slice(-2)) || 0

            // Enforce limits (no limit on hours, 59 max for minutes and seconds)
            const validMinutes = Math.min(minutes, 59)
            const validSeconds = Math.min(seconds, 59)

            formattedValue = `${hours.toString().padStart(2, '0')}:${validMinutes.toString().padStart(2, '0')}:${validSeconds.toString().padStart(2, '0')}`
        } else {
            formattedValue = digits
        }
    } else {
        // Format as MM:SS (original logic)
        if (digits.length >= 1) {
            const minutes = parseInt(digits.slice(0, -2)) || 0
            const seconds = parseInt(digits.slice(-2)) || 0

            // Enforce limits
            const validMinutes = Math.min(minutes, 59)
            const validSeconds = Math.min(seconds, 59)

            formattedValue = `${validMinutes.toString().padStart(2, '0')}:${validSeconds.toString().padStart(2, '0')}`
        } else {
            formattedValue = digits
        }
    }

    displayValue.value = formattedValue

    // Check if we have a complete time format
    const expectedColons = props.includeHours ? 2 : 1
    if (formattedValue.split(':').length - 1 === expectedColons) {
        const seconds = displayToSeconds(formattedValue)
        emit('update:modelValue', formattedValue)
        emit('update:seconds', seconds)
    } else {
        emit('update:modelValue', formattedValue)
    }
}

// Handle keydown to prevent invalid input
const handleKeydown = (event) => {
    // Allow: backspace, delete, tab, escape, enter
    if ([46, 8, 9, 27, 13].indexOf(event.keyCode) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (event.keyCode === 65 && (event.ctrlKey === true || event.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (event.keyCode >= 35 && event.keyCode <= 40)) {
        return
    }
    // Ensure that it is a number and stop the keypress if not
    if ((event.shiftKey || (event.keyCode < 48 || event.keyCode > 57)) &&
        (event.keyCode < 96 || event.keyCode > 105)) {
        event.preventDefault()
    }
}
</script>

<template>
    <DSInput
        :model-value="displayValue"
        @update:model-value="handleInput"
        @keydown="handleKeydown"
        type="text"
        inputmode="numeric"
        :label="label"
        :error="error"
        :required="required"
        :placeholder="computedPlaceholder"
        :maxlength="computedMaxLength"
    />
</template>

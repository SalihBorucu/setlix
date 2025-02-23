<script setup>
import { ref, watch } from 'vue'
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
        default: '00:00'
    }
})

const emit = defineEmits(['update:modelValue', 'update:seconds'])

// Internal display value in MM:SS format
const displayValue = ref('')

// Convert seconds to MM:SS format
const secondsToDisplay = (seconds) => {
    if (!seconds) return ''
    const minutes = Math.floor(seconds / 60)
    const remainingSeconds = seconds % 60
    return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`
}

// Convert MM:SS to seconds
const displayToSeconds = (value) => {
    const [minutes, seconds] = value.split(':').map(Number)
    return (minutes * 60) + seconds
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

    // Keep only the last 4 digits entered
    const digits = value.replace(/[^0-9]/g, '').slice(-4)
    
    if (!digits) {
        displayValue.value = ''
        emit('update:modelValue', '')
        return
    }

    let formattedValue = ''

    if (digits.length >= 3) {
        // Format as MM:SS
        const minutes = parseInt(digits.slice(0, -2))
        const seconds = parseInt(digits.slice(-2))
        
        // Enforce limits
        const validMinutes = Math.min(minutes, 59)
        const validSeconds = Math.min(seconds, 59)
        
        formattedValue = `${validMinutes.toString().padStart(2, '0')}:${validSeconds.toString().padStart(2, '0')}`
    } else {
        formattedValue = digits
    }

    displayValue.value = formattedValue

    if (formattedValue.includes(':')) {
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
        :placeholder="placeholder"
        maxlength="5"
    />
</template> 
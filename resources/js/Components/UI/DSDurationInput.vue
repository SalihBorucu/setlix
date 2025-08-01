<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { DSInput } from '@/Components/UI'
import IMask from 'imask'

const props = defineProps({
    modelValue: {
        type: [String, Number],
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
    includeHours: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue', 'update:seconds'])

const inputComponentRef = ref(null)
let maskInstance = null
// --- The flag to prevent feedback loops ---
let isUpdatingInternally = false

const displayToSeconds = (value) => {
    if (typeof value === 'number') return value;
    if (!value || typeof value !== 'string' || value.indexOf(':') === -1) {
        return 0
    }
    const parts = value.split(':').map(Number)
    if (parts.length === 3) {
        return (parts[0] * 3600) + (parts[1] * 60) + (parts[2] || 0)
    }
    return (parts[0] * 60) + (parts[1] || 0)
}

const secondsToDisplay = (totalSeconds) => {
    if (typeof totalSeconds !== 'number' || isNaN(totalSeconds)) return ''

    if (props.includeHours) {
        const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0')
        const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0')
        const s = (totalSeconds % 60).toString().padStart(2, '0')
        return `${h}:${m}:${s}`
    } else {
        const m = Math.floor(totalSeconds / 60).toString().padStart(2, '0')
        const s = (totalSeconds % 60).toString().padStart(2, '0')
        return `${m}:${s}`
    }
}

const setupMask = () => {
    if (maskInstance) {
        maskInstance.destroy()
    }

    const inputEl = inputComponentRef.value?.$el?.querySelector('input') || inputComponentRef.value
    if (!inputEl) return;

    const hasInitialValue = props.modelValue != null && props.modelValue !== '';

    const maskOptions = {
        mask: props.includeHours ? 'HH:MM:SS' : 'MM:SS',
        lazy: !hasInitialValue,
        blocks: {
            HH: { mask: IMask.MaskedRange, from: 0, to: 99, minLength: 2, autofix: 'pad' },
            MM: { mask: IMask.MaskedRange, from: 0, to: props.includeHours ? 59 : 99, minLength: 2, autofix: 'pad' },
            SS: { mask: IMask.MaskedRange, from: 0, to: 59, minLength: 2, autofix: 'pad' }
        }
    };

    maskInstance = IMask(inputEl, maskOptions);

    if (hasInitialValue) {
        const initialSeconds = displayToSeconds(props.modelValue);
        maskInstance.value = secondsToDisplay(initialSeconds);
    }

    maskInstance.on('accept', () => {
        // Set the flag before emitting
        isUpdatingInternally = true;

        const maskedValue = maskInstance.value;
        const totalSeconds = displayToSeconds(maskedValue);
        emit('update:modelValue', maskedValue);
        emit('update:seconds', totalSeconds);

        // Unset the flag after the update cycle
        nextTick(() => {
            isUpdatingInternally = false;
        });
    });
}

// Watch for external changes to v-model
watch(() => props.modelValue, (newValue) => {
    // If the flag is set, it means the change came from our own component, so we ignore it.
    if (isUpdatingInternally) {
        return;
    }

    if (!maskInstance) return;

    const newSeconds = displayToSeconds(newValue || 0);
    maskInstance.value = secondsToDisplay(newSeconds);

}, { deep: true }); // Use deep or immediate to ensure it catches all cases

watch(() => props.includeHours, setupMask);

onMounted(setupMask);

onUnmounted(() => {
    if (maskInstance) {
        maskInstance.destroy();
    }
});
</script>

<template>
    <DSInput
        ref="inputComponentRef"
        :label="label"
        :error="error"
        :required="required"
        :placeholder="props.includeHours ? '00:00:00' : '00:00'"
        inputmode="numeric"
    />
</template>

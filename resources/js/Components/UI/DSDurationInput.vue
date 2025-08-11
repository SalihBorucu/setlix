<script setup>
import {ref, watch, nextTick, computed} from 'vue'
import Input from "@/Components/UI/Input.vue";

// --- Component API (Props and Emits) ---
// Unchanged
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


// --- Internal State Management ---
const hours = ref('')
const minutes = ref('')
const seconds = ref('')

const hourInputRef = ref(null)
const minuteInputRef = ref(null)
const secondInputRef = ref(null)

let isUpdatingInternally = false


// --- Helper Functions ---
const formatValue = (val) => String(val || '').padStart(2, '0');

// --- START: Added logic for smarter label styling ---
const isOptional = computed(() => {
    // If required is true, it can't be optional. Otherwise check for the text.
    return !props.required && props.label && props.label.includes('(Optional)');
});

const displayLabel = computed(() => {
    if (!props.label) return '';
    // Removes the optional tag for clean display
    return props.label.replace('(Optional)', '').trim();
});
// --- END: Added logic for smarter label styling ---


// --- Synchronization Logic ---

// Watch for changes from the parent component (v-model)
watch(() => props.modelValue, (newValue) => {
    if (isUpdatingInternally) {
        return;
    }

    const parts = (newValue || '0:0').split(':').map(p => p || '0');

    // if (props.includeHours) {
    //     hours.value = parts[0] || '';
    //     minutes.value = parts[1] || '';
    //     seconds.value = parts[2] || '';
    // } else {
    //     hours.value = '';
    //     minutes.value = parts[0] || '';
    //     seconds.value = parts[1] || '';
    // }
}, {immediate: true})

// Watch for user input to emit updates
watch([hours, minutes, seconds], ([h, m, s]) => {
    const currentHours = parseInt(h, 10) || 0;
    const currentMinutes = parseInt(m, 10) || 0;
    const currentSeconds = parseInt(s, 10) || 0;

    let displayValue = '';
    let totalSeconds = 0;

    if (props.includeHours) {
        displayValue = `${formatValue(h)}:${formatValue(m)}:${formatValue(s)}`;
        totalSeconds = (currentHours * 3600) + (currentMinutes * 60) + currentSeconds;
    } else {
        displayValue = `${formatValue(m)}:${formatValue(s)}`;
        totalSeconds = (currentMinutes * 60) + currentSeconds;
    }

    if (displayValue === props.modelValue) {
        return;
    }

    isUpdatingInternally = true;
    emit('update:modelValue', displayValue);
    emit('update:seconds', totalSeconds);
    nextTick(() => {
        isUpdatingInternally = false;
    });

}, {deep: true})

// Add immediate watchers for validation
watch(hours, (newValue) => {
    if (parseInt(newValue, 10) > 59) {
        hours.value = '99';
    }
});

watch(minutes, (newValue) => {
    if (parseInt(newValue, 10) > 59) {
        minutes.value = '59';
    }
});

watch(seconds, (newValue) => {
    if (parseInt(newValue, 10) > 59) {
        seconds.value = '59';
    }
});


// --- User Experience (UX) Handlers ---
const handleInput = (part, nextElRef) => {
    if (part.value.length === 2 && nextElRef?.value) {
        nextElRef.value.focus();
        nextElRef.value.select();
    }
}

const handleKeydown = (event, prevElRef) => {
    if (event.key === 'Backspace' && event.target.value === '' && prevElRef?.value) {
        prevElRef.value.focus();
    }
}

const handleBlur = (partRef) => {
    if (partRef.value) {
        partRef.value = partRef.value.padStart(2, '0');
    }
};

// --- Common classes for the inputs to keep them consistent ---
const inputClasses = "block w-14 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-center sm:text-sm";

</script>

<template>
    <div class="space-y-1">
        <label v-if="label" class="block text-sm font-medium text-neutral-700 text-left">
            {{ label }}
        </label>
        <div class="flex items-center space-x-2">
            <template v-if="props.includeHours">
                <input
                    ref="hourInputRef"
                    v-model="hours"
                    type="number"
                    min="0"
                    max="59"
                    inputmode="numeric"
                    maxlength="2"
                    placeholder="hh"
                    :class="[
                        'w-12 rounded-lg border-neutral-300 shadow-sm transition-colors duration-200 no-spinner',
                        'focus:border-primary-500 focus:ring-primary-500',
                        { 'border-error-500 focus:border-error-500 focus:ring-error-500': error }
                    ]"
                    @input="handleInput(hours, minuteInputRef)"
                    @blur="handleBlur(hours)"
                />
                <span class="text-gray-500 -ml-1">:</span>
            </template>

            <input
                ref="minuteInputRef"
                v-model="minutes"
                type="number"
                min="0"
                max="59"
                inputmode="numeric"
                maxlength="2"
                placeholder="mm"
                :class="[
                        'w-12 rounded-lg border-neutral-300 shadow-sm transition-colors duration-200 no-spinner',
                        'focus:border-primary-500 focus:ring-primary-500',
                        { 'border-error-500 focus:border-error-500 focus:ring-error-500': error }
                ]"
                @input="handleInput(minutes, secondInputRef)"
                @keydown="handleKeydown($event, hourInputRef)"
                @blur="handleBlur(minutes)"
            />

            <span class="text-gray-500 -ml-1">:</span>

            <input
                ref="secondInputRef"
                v-model="seconds"
                type="number"
                min="0"
                max="59"
                inputmode="numeric"
                maxlength="2"
                placeholder="ss"
                :class="[
                        'w-12 rounded-lg border-neutral-300 shadow-sm transition-colors duration-200 no-spinner',
                        'focus:border-primary-500 focus:ring-primary-500',
                        { 'border-error-500 focus:border-error-500 focus:ring-error-500': error }
                ]"
                @keydown="handleKeydown($event, minuteInputRef)"
                @blur="handleBlur(seconds)"
            />
        </div>
        <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    </div>
</template>

<style scoped>
.no-spinner::-webkit-outer-spin-button,
.no-spinner::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.no-spinner {
    -moz-appearance: textfield; /* For Firefox */
}
</style>

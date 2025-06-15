<script setup>
import { DSInput, DSCard, DSDurationInput } from '@/Components/UI'

const props = defineProps({
    form: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['update:targetDuration'])
</script>

<template>
    <DSCard>
        <div class="p-6 space-y-6">
            <div>
                <DSInput
                    v-model="form.name"
                    type="text"
                    label="Setlist Name"
                    :error="form.errors.name"
                    required
                />
                <p class="mt-1 text-sm text-neutral-500">
                    Give your setlist a memorable name
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral-700">
                    Description (Optional)
                </label>
                <div class="mt-1">
                    <textarea
                        v-model="form.description"
                        rows="3"
                        :class="[
                            'block w-full rounded-lg shadow-sm transition-colors duration-200',
                            'border-neutral-300 focus:border-primary-500 focus:ring-primary-500',
                            { 'border-error-500 focus:border-error-500 focus:ring-error-500': form.errors.description }
                        ]"
                    />
                </div>
                <p class="mt-1 text-sm text-neutral-500">
                    Add any notes or context about this setlist
                </p>
            </div>

            <div>
                <DSDurationInput
                    v-model="form.target_duration"
                    @update:seconds="(seconds) => form.target_duration_seconds = seconds"
                    label="Target Duration"
                    placeholder="00:00:00"
                    include-hours
                    :error="form.errors.target_duration"
                    required
                />
                <p class="mt-1 text-sm text-neutral-500">
                    Set the target duration for this event (required for sharing with clients so that your clients know how long your performance will be).
                </p>
            </div>
        </div>
    </DSCard>
</template>

<script setup>
import { DSInput, DSCard } from '@/Components/UI'

defineProps({
    form: {
        type: Object,
        required: true
    }
})
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
        </div>
    </DSCard>
</template> 
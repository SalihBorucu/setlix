<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSInput, DSCard, DSAlert } from '@/Components/UI'
import ImageUpload from '@/Components/UI/ImageUpload.vue'

const form = useForm({
    name: '',
    description: '',
    cover_image: null,
})

const submit = () => {
    form.post(route('bands.store'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Create Band" />

    <AuthenticatedLayout>
        <template #header>
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 text-neutral-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Create New Band
                    </h2>
                    <p class="mt-1 text-sm text-neutral-500">
                        Get started by creating a new band and inviting your members
                    </p>
                </div>
            </div>
        </template>

        <DSCard class="max-w-2xl">
            <form @submit.prevent="submit" class="space-y-6 p-6">
                <!-- Error Message -->
                <DSAlert 
                    v-if="Object.keys(form.errors).length > 0" 
                    type="error"
                >
                    <ul class="list-disc list-inside">
                        <li v-for="error in form.errors" :key="error">{{ error }}</li>
                    </ul>
                </DSAlert>

                <!-- Band Name -->
                <div>
                    <DSInput
                        v-model="form.name"
                        type="text"
                        label="Band Name"
                        :error="form.errors.name"
                        required
                    />
                    <p class="mt-1 text-sm text-neutral-500">
                        Choose a unique name for your band
                    </p>
                </div>

                <!-- Band Description -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700">
                        Description
                    </label>
                    <div class="mt-1">
                        <textarea
                            v-model="form.description"
                            rows="4"
                            :class="[
                                'block w-full rounded-lg shadow-sm transition-colors duration-200',
                                'border-neutral-300 focus:border-primary-500 focus:ring-primary-500',
                                { 'border-error-500 focus:border-error-500 focus:ring-error-500': form.errors.description }
                            ]"
                        />
                    </div>
                    <p class="mt-1 text-sm text-neutral-500">
                        Brief description of your band and its style
                    </p>
                </div>

                <!-- Cover Image -->
                <div>
                    <label class="block text-sm font-medium text-neutral-700">
                        Cover Image
                    </label>
                    <ImageUpload
                        v-model="form.cover_image"
                        :error="form.errors.cover_image"
                    />
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4">
                    <DSButton
                        type="button"
                        variant="outline"
                        :href="route('dashboard')"
                    >
                        Cancel
                    </DSButton>
                    <DSButton
                        type="submit"
                        variant="primary"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Creating...</span>
                        <span v-else>Create Band</span>
                    </DSButton>
                </div>
            </form>
        </DSCard>
    </AuthenticatedLayout>
</template> 
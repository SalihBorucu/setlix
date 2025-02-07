<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { DSButton, DSInput, DSCard, DSAlert } from '@/Components/UI'

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

const handleImageUpload = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.cover_image = file
    }
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
                    <div class="mt-1 flex justify-center rounded-lg border border-dashed border-neutral-300 px-6 py-10">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="mt-4 flex text-sm text-neutral-600">
                                <label
                                    for="cover-image"
                                    class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500"
                                >
                                    <span>Upload a file</span>
                                    <input 
                                        id="cover-image" 
                                        type="file" 
                                        class="sr-only"
                                        accept="image/*"
                                        @change="handleImageUpload"
                                    >
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-neutral-500">PNG, JPG, GIF up to 10MB</p>
                        </div>
                    </div>
                    <p v-if="form.errors.cover_image" class="mt-1 text-sm text-error-500">
                        {{ form.errors.cover_image }}
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end space-x-4">
                    <DSButton
                        type="button"
                        variant="outline"
                        :href="route('bands.index')"
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
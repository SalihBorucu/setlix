<script setup>
import { ref, watch } from 'vue'
import { DSAlert } from '@/Components/UI'

const props = defineProps({
    modelValue: {
        type: [File, null],
        default: null
    },
    error: {
        type: String,
        default: null
    },
    previewUrl: {
        type: String,
        default: null
    }
})

const emit = defineEmits(['update:modelValue'])

const previewImage = ref(props.previewUrl)
const dragOver = ref(false)

watch(() => props.modelValue, (newFile) => {
    if (newFile) {
        const reader = new FileReader()
        reader.onload = e => previewImage.value = e.target.result
        reader.readAsDataURL(newFile)
    } else {
        previewImage.value = props.previewUrl
    }
})

const handleFileInput = (e) => {
    const file = e.target.files?.[0]
    if (file) {
        emit('update:modelValue', file)
    }
}

const handleDrop = (e) => {
    e.preventDefault()
    dragOver.value = false

    const file = e.dataTransfer?.files?.[0]
    if (file?.type.startsWith('image/')) {
        emit('update:modelValue', file)
    }
}

const handleDragOver = (e) => {
    e.preventDefault()
    dragOver.value = true
}

const handleDragLeave = () => {
    dragOver.value = false
}
</script>

<template>
    <div>
        <DSAlert v-if="error" type="error" class="mb-4">
            {{ error }}
        </DSAlert>

        <div
            class="relative flex justify-center rounded-lg border border-dashed px-6 py-10 transition-colors duration-200"
            :class="[
                dragOver ? 'border-primary-500 bg-primary-50' : 'border-neutral-300',
                { 'border-error-500': error }
            ]"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            @drop="handleDrop"
        >
            <div class="text-center">
                <!-- Preview Image -->
                <div v-if="previewImage" class="mb-4">
                    <img
                        :src="previewImage"
                        class="mx-auto h-40 w-40 rounded-lg object-cover"
                        alt="Cover preview"
                    />
                </div>

                <!-- Upload Icon -->
                <svg
                    v-else
                    class="mx-auto h-12 w-12 text-neutral-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                </svg>

                <div class="mt-4 flex text-sm text-neutral-600">
                    <label
                        class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500"
                    >
                        <span>Upload a file</span>
                        <input
                            type="file"
                            class="sr-only"
                            accept="image/*"
                            @change="handleFileInput"
                        >
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>

                <p class="text-xs text-neutral-500">
                    PNG, JPG, GIF up to 2MB
                </p>
            </div>
        </div>
    </div>
</template>

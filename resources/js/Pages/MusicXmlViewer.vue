<template>
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">MusicXML Viewer</h1>

        <div class="mb-4 flex flex-wrap gap-2">
            <button @click="zoomOut" class="px-3 py-1 bg-gray-200 rounded">- Zoom Out</button>
            <button @click="zoomIn" class="px-3 py-1 bg-gray-200 rounded">+ Zoom In</button>
            <button @click="transposeDown" class="px-3 py-1 bg-gray-200 rounded">↓ Transpose Down</button>
            <button @click="transposeUp" class="px-3 py-1 bg-gray-200 rounded">↑ Transpose Up</button>
            <button @click="resetTranspose" class="px-3 py-1 bg-blue-200 rounded">Reset Transpose</button>
            <span class="px-3 py-1 bg-green-100 rounded">Semitones: {{ transposeValue }}</span>
        </div>

        <div ref="notation"></div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import verovio from 'verovio'

// Props passed from Inertia
const props = defineProps({
    music: String
})

const notation = ref(null)
let toolkit = null
const scale = ref(50)
const transposeValue = ref(0)
const originalData = ref(null)

const renderMusic = () => {
    if (!toolkit) {
        toolkit = new verovio.toolkit()
    }

    // Build options object
    const options = {
        scale: scale.value,
        pageWidth: 1500,
        footer: 'none',
        header: 'none'
    }

    // Only add transpose option if it's not 0
    if (transposeValue.value !== 0) {
        options.transpose = transposeValue.value.toString()
    }

    toolkit.setOptions(options)

    try {
        if (originalData.value) {
            toolkit.loadData(originalData.value)
            const svg = toolkit.renderToSVG(1, {})
            notation.value.innerHTML = svg
        }
    } catch (error) {
        notation.value.innerHTML = '<p class="text-red-500">Failed to load MusicXML.</p>'
        console.error(error)
    }
}

const zoomIn = () => {
    scale.value += 10
    renderMusic()
}

const zoomOut = () => {
    scale.value = Math.max(10, scale.value - 10)
    renderMusic()
}

const transposeUp = () => {
    transposeValue.value += 1
    renderMusic()
}

const transposeDown = () => {
    transposeValue.value -= 1
    renderMusic()
}

const resetTranspose = () => {
    transposeValue.value = 0
    renderMusic()
}

onMounted(() => {
    setTimeout(() => {
        originalData.value = props.music
        renderMusic()
    }, 500)
})
</script>

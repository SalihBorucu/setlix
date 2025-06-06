<template>
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">{{ song.name }}</h1>

        <div class="mb-4 flex flex-wrap gap-2">
            <DSButton :disabled="pageIsLoading" @click="zoomOut">- Zoom Out</DSButton>
            <DSButton :disabled="pageIsLoading" @click="zoomIn">+ Zoom In</DSButton>
            <DSButton :disabled="pageIsLoading" variant="secondary" @click="transposeDown">↓ Transpose Down</DSButton>
            <DSButton :disabled="pageIsLoading" variant="secondary" @click="transposeUp">↑ Transpose Up</DSButton>
            <DSButton :disabled="pageIsLoading" variant="outline" @click="resetTranspose">Reset Transpose</DSButton>
        </div>

        <div class="mb-2 text-sm text-gray-600">
            <span>Page {{ currentPage }} of {{ totalPages }} -- Zoom: {{ scale }} %</span>
            <span v-if="transposeValue"> -- Transposed: {{ transposeValue }} Semitones</span>
        </div>

        <div ref="notation" class="music-container"></div>

        <!-- Loading indicator -->
        <div v-if="pageIsLoading" class="flex justify-center py-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        </div>

        <!-- Scroll sentinel for triggering next page load -->
        <div ref="scrollSentinel"></div>
    </div>
</template>

<script setup>
import { DSButton } from '@/Components/UI'
import {ref, onMounted, onUnmounted, nextTick, computed} from 'vue'
import verovio from 'verovio'
import {useWindowSize} from "@/Composables/useWindowSize.js";

// Props passed from Inertia
const props = defineProps({
    music: String,
    song: Object
})

const notation = ref(null)
const scrollSentinel = ref(null)
let toolkit = null
const scale = ref(50)
const transposeValue = ref(0)
const originalData = ref(null)
const currentPage = ref(1)
const totalPages = ref(1)
const renderedPages = ref(new Set([]))
const pageIsLoading = ref(false)
let intersectionObserver = null

// const {width, height} = useWindowSize();
const loading = ref(false);
const {width, height} = useWindowSize();
const isLandscape = computed(() => width.value > height.value);

const decideOnInitialZoomWithCategories = () => {
    const w = width.value;
    // DESKTOP MONITORS
    if (w >= 3440) scale.value = 75;  // Ultra-wide 21:9 monitors
    else if (w >= 2560) scale.value = 70;  // 4K/QHD+ monitors
    else if (w >= 2048) scale.value = 65;  // QHD monitors
    else if (w >= 1920) scale.value = 60;  // Full HD monitors
    else if (w >= 1680) scale.value = 55;  // WSXGA+ monitors
    else if (w >= 1440) scale.value = 52;  // Wide monitors
    else if (w >= 1366) scale.value = 45;  // HD+ monitors
    else if (w >= 1280) scale.value = 40;  // SXGA monitors

    // LAPTOPS
    else if (w >= 1200) scale.value = 45;  // Large laptops
    else if (w >= 1024) scale.value = 35;  // Medium laptops

    // TABLETS
    else if (w >= 768) scale.value = 25;   // iPad/large tablets landscape
    else if (w >= 640) scale.value = 20;   // Small tablets landscape

    // MOBILE PHONES
    else if (w >= 480) scale.value = 25;   // Large phones landscape
    else if (w >= 414) scale.value = 22;   // iPhone Plus/Max sizes
    else if (w >= 375) scale.value = 15;   // Standard iPhone sizes
    else if (w >= 320) scale.value = 15;   // Small phones
    else scale.value = 15;                 // Very small screens
};

const initializeToolkit = () => {
    if (!toolkit) {
        toolkit = new verovio.toolkit()
    }

    // Build options object
    const options = {
        scale: scale.value,
        pageWidth: width.value * 100 / scale.value,
        // pageHeight: height.value,
        landscape: isLandscape.value,
        adjustPageWidth: true,
    }

    // Only add transpose option if it's not 0
    if (transposeValue.value !== 0) {
        options.transpose = transposeValue.value.toString()
    }

    toolkit.setOptions(options)
}

const loadMusicData = () => {
    if (!originalData.value) return

    initializeToolkit()

    try {
        toolkit.loadData(originalData.value)
        totalPages.value = toolkit.getPageCount()

        // Clear existing content and rendered pages
        notation.value.innerHTML = ''
        renderedPages.value.clear()

        // Load first page
        loadPage(1)
    } catch (error) {
        notation.value.innerHTML = '<p class="text-red-500">Failed to load MusicXML.</p>'
        console.error(error)
    }
}

const loadPage = async (pageNum) => {
    if (renderedPages.value.has(pageNum) || pageNum > totalPages.value || pageIsLoading.value) {
        return
    }

    pageIsLoading.value = true

    try {
        await nextTick()

        const svg = toolkit.renderToSVG(pageNum, {})

        // Create a container for this page
        const pageContainer = document.createElement('div')
        pageContainer.className = 'page-container mb-4'
        pageContainer.setAttribute('data-page', pageNum)
        pageContainer.innerHTML = svg

        // Insert page in correct order
        const existingPages = notation.value.querySelectorAll('.page-container')
        let inserted = false

        for (let i = 0; i < existingPages.length; i++) {
            const existingPageNum = parseInt(existingPages[i].getAttribute('data-page'))
            if (pageNum < existingPageNum) {
                notation.value.insertBefore(pageContainer, existingPages[i])
                inserted = true
                break
            }
        }

        if (!inserted) {
            notation.value.appendChild(pageContainer)
        }

        renderedPages.value.add(pageNum)

        // Update current page based on scroll position
        updateCurrentPage()

    } catch (error) {
        console.error(`Failed to load page ${pageNum}:`, error)
    } finally {
        pageIsLoading.value = false
    }
}

const loadNextPages = () => {
    // Load next 2 pages if available
    const nextPage = Math.max(...Array.from(renderedPages.value)) + 1

    if (nextPage <= totalPages.value) {
        loadPage(nextPage)

        // Load one more page ahead
        if (nextPage + 1 <= totalPages.value) {
            setTimeout(() => loadPage(nextPage + 1), 100)
        }
    }
}

const updateCurrentPage = () => {
    const pages = notation.value.querySelectorAll('.page-container')
    const viewportHeight = window.innerHeight
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop

    for (let i = 0; i < pages.length; i++) {
        const pageRect = pages[i].getBoundingClientRect()
        const pageTop = pageRect.top + scrollTop
        const pageBottom = pageTop + pageRect.height

        // Check if page is currently in view
        if (pageTop <= scrollTop + viewportHeight / 2 && pageBottom >= scrollTop + viewportHeight / 2) {
            currentPage.value = parseInt(pages[i].getAttribute('data-page'))
            break
        }
    }
}

const setupIntersectionObserver = () => {
    if (!scrollSentinel.value) return

    intersectionObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadNextPages()
                }
            })
        },
        {
            rootMargin: '200px' // Start loading when sentinel is 200px away from viewport
        }
    )

    intersectionObserver.observe(scrollSentinel.value)
}

const handleScroll = () => {
    updateCurrentPage()
}

const renderMusic = () => {
    loadMusicData()
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
        decideOnInitialZoomWithCategories()
        renderMusic()

        nextTick(() => {
            setupIntersectionObserver()
            window.addEventListener('scroll', handleScroll)
        })
    }, 500)
})

onUnmounted(() => {
    if (intersectionObserver) {
        intersectionObserver.disconnect()
    }
    window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.music-container {
    min-height: 100vh;
    min-width: 100vw;
}

.page-container {
    border-bottom: 1px solid #e5e7eb;
    padding-bottom: 1rem;
}

.page-container:last-child {
    border-bottom: none;
}
</style>

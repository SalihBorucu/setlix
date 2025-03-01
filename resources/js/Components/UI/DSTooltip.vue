<template>
    <Tooltip
        :delay="{ show: 100, hide: 0 }"
        placement="top"
        theme="tooltip"
    >
        <slot></slot>
        
        <template #popper>
            <div class="text-sm font-medium">
                <slot name="content"></slot>
            </div>
        </template>
    </Tooltip>
</template>

<script setup>
import { Tooltip } from 'floating-vue'
import 'floating-vue/dist/style.css'
</script>

<style>
.v-popper--theme-tooltip .v-popper__inner {
    @apply bg-neutral-900 text-white px-3 py-2 rounded-lg;
}

.v-popper--theme-tooltip .v-popper__arrow-inner,
.v-popper--theme-tooltip .v-popper__arrow-outer {
    @apply border-neutral-900;
}
</style> 
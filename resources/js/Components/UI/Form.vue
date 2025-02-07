<script setup>
defineEmits(['submit'])
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-6">
        <slot></slot>
    </form>
</template> 
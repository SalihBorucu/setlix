<template>
  <div class="relative">
    <span class="absolute z-50 text-xs text-primary-600 animate-float-up"
          :class="positionClass"
          v-show="isCopying"
    >
      Copied
    </span>
      <slot :copy-text="copyText">
        <DocumentDuplicateIcon name="duplicate"
                               class="mr-2 h-5 w-5 cursor-pointer text-primary-600 hover:text-primary-800"
                               @click="copyText"
        />
      </slot>
  </div>
</template>

<script setup>
import {DocumentDuplicateIcon} from "@heroicons/vue/24/outline";
import {computed, ref} from "vue";
import copyToClipboard from "@/Utilities/copyToClipboard.js";

const props = defineProps({
  text: {
    required: true,
    type: String,
  },
  textType: String,
  position: {
    type: String,
    default: 'top'
  }
})

const isCopying = ref(false)

const copyText = () => {
  isCopying.value = true
  copyToClipboard(props.text)
  setTimeout(() => isCopying.value = false, 600);
}

const positionClass = computed(() => {
  if (props.position === 'top') {
    return '-left-2.5'
  }

  if (props.position === 'left') {
    return '-left-11 top-5'
  }

    if (props.position === 'right') {
        return 'right-2 top-0.5'
    }
})

</script>

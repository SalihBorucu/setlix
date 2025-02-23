<template>
  <div v-if="shouldShowBanner" class="bg-blue-50 border-b border-blue-200">
    <div class="max-w-7xl mx-auto p-3">
      <div class="flex items-center justify-between">
        <!-- Trial Status -->
        <div class="flex items-center space-x-2">
          <span v-if="remainingDays > 0" class="text-sm text-blue-700">
            {{ remainingDays }} days remaining in your free trial
          </span>
          <span v-else class="text-sm text-red-700">
            Trial expired
          </span>
        </div>

        <!-- Trial Limits -->
        <div v-if="trialLimitReached" class="text-sm text-red-600">
          {{ trialLimitMessage }}
        </div>

        <!-- Subscribe Button -->
        <Link
          v-if="!isSubscribed && bandId"
          :href="route('bands.subscribe', bandId)"
        >
          <DSButton variant="primary" size="sm">
            Subscribe Now
          </DSButton>
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { DSButton } from '@/Components/UI';

const page = usePage();

const trial = computed(() => page.props.trial || {});
const isSubscribed = computed(() => trial.value?.isSubscribed ?? false);
const remainingDays = computed(() => trial.value?.remainingDays ?? 0);
const trialLimitReached = computed(() => trial.value?.limitReached ?? false);
const trialLimitMessage = computed(() => trial.value?.limitMessage ?? '');
const bandId = computed(() => trial.value?.bandId);

// Don't show banner on subscribe page
const shouldShowBanner = computed(() => {
  const isSubscribePage = route().current('bands.subscribe');
  return trial.value && !isSubscribed.value && !isSubscribePage && bandId.value;
});
</script> 
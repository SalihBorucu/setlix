<template>
  <div v-if="showBanner" class="bg-blue-50 border-b border-blue-200">
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
        <DSButton
          v-if="!isSubscribed"
          variant="primary"
          size="sm"
          :href="route('subscription.checkout')"
        >
          Subscribe Now
        </DSButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { DSButton } from '@/Components/UI';

const page = usePage();

const trial = computed(() => page.props.trial || {});
const isSubscribed = computed(() => trial.value?.isSubscribed ?? false);
const remainingDays = computed(() => trial.value?.remainingDays ?? 0);
const trialLimitReached = computed(() => trial.value?.limitReached ?? false);
const trialLimitMessage = computed(() => trial.value?.limitMessage ?? '');
const showBanner = computed(() => trial.value && !isSubscribed.value);
</script> 
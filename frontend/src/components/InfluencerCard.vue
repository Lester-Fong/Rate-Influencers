<template>
  <router-link
    :to="{ name: 'influencer', params: { influencerSlug: data.slug } }"
    class="ir-container group flex min-h-64 flex-col items-center px-5 pb-6 pt-0 transition-transform hover:-translate-y-1"
    :aria-label="`View reviews for ${data.name}`"
  >
    <img
      v-if="data.profile_picture && !imageFailed"
      :src="data.profile_picture"
      :alt="`${data.name} profile`"
      class="ir-card-avatar"
      @error="imageFailed = true"
    />
    <div v-else class="ir-card-avatar text-4xl font-bold" aria-hidden="true">
      {{ data.name.charAt(0).toUpperCase() }}
    </div>

    <div class="mt-4 flex flex-1 flex-col items-center justify-center gap-2 text-center">
      <h2 class="ir-text ir-text-main text-xl font-semibold group-hover:text-white">{{ data.name }}</h2>
      <star-rating
        :star-size="20"
        :increment="0.1"
        active-color="#a7d708"
        inactive-color="transparent"
        border-color="#F4978E"
        :border-width="1"
        :show-rating="false"
        :read-only="true"
        :display-only="true"
        :rating="Number(data.rating || 0)"
      />
      <p class="ir-text ir-text-secondary text-sm">{{ Number(data.rating || 0).toFixed(1) }} - {{ reviewLabel }}</p>
    </div>
  </router-link>
</template>

<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});

const imageFailed = ref(false);

watch(() => props.data.profile_picture, () => {
  imageFailed.value = false;
});

const reviewLabel = computed(() => {
  const count = Number(props.data.review_count || 0);
  return `${count} ${count === 1 ? "review" : "reviews"}`;
});
</script>

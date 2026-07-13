<template>
  <article class="mb-3">
    <div class="flex items-center justify-between gap-4 pb-3">
      <h2 class="text-white ir-text">{{ review.reviewer_name }}</h2>
      <star-rating
        :star-size="18"
        :show-rating="false"
        :read-only="true"
        :display-only="true"
        :rating="review.rating"
        active-color="#a7d708"
        inactive-color="transparent"
        border-color="#F4978E"
        :border-width="1"
      />
    </div>
    <p class="ir-text ir-text-secondary">{{ review.review }}</p>
    <hr class="mt-4 border-emerald-950 border-y-2" />
  </article>
</template>

<script setup>
defineProps({
  review: {
    type: Object,
    required: true,
  },
});
</script>

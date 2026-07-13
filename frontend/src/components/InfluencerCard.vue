<template>
  <article
    class="ir-container flex cursor-pointer flex-col items-center px-5 pb-6 pt-10 transition-transform hover:-translate-y-1"
    tabindex="0"
    role="link"
    @click="$emit('click')"
    @keyup.enter="$emit('click')"
  >
    <img
      v-if="data.profile_picture"
      :src="data.profile_picture"
      :alt="`${data.name} profile`"
      class="ir-avatar rounded-full object-cover"
    />
    <div v-else class="ir-avatar flex items-center justify-center rounded-full bg-emerald-950 text-4xl font-bold text-emerald-200" aria-hidden="true">
      {{ data.name.charAt(0).toUpperCase() }}
    </div>

    <div class="mt-4 flex flex-col items-center gap-2 text-center">
      <h2 class="ir-text ir-text-main text-2xl">{{ data.name }}</h2>
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
      <p class="ir-text ir-text-secondary text-sm">{{ Number(data.rating || 0).toFixed(1) }} · {{ reviewLabel }}</p>
    </div>
  </article>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});

defineEmits(["click"]);

const reviewLabel = computed(() => {
  const count = Number(props.data.review_count || 0);
  return `${count} ${count === 1 ? "review" : "reviews"}`;
});
</script>

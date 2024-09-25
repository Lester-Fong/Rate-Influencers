<template>
  <div class="ir-container flex justify-center mt-10 pt-20 items-center pb-5 cursor-pointer">
    <img src="https://picsum.photos/500/500" class="rounded-full ir-avatar" alt="img-1" />
    <div class="flex items-center justify-between flex-col gap-1">
      <h1 class="ir-text ir-text-main text-2xl mt-3">{{ influencer?.name }}</h1>
      <star-rating :star-size="20" :increment="0.5" active-color="#a7d708" inactive-color="transparent" border-color="#F4978E" :border-width="1" :show-rating="false" :read-only="true" :display-only="true" :rating="influencer?.rating"> </star-rating>
      <p class="ir-text ir-text-secondary text-center text-sm mt-2">12,345 ratings</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
let props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});

const influencer = ref({});
influencer.value = props.data;
</script>


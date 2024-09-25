<template>
  <div class="mb-3">
    <h1 class="text-white ir-text pb-3">{{ comment.name }}</h1>
    <p class="ir-text ir-text-secondary">
      {{ comment.comment }}
    </p>
    <div class="flex justify-end items-end mb-2">
      <span class="cursor-pointer" @click="handleLikeReact">
        <v-icon name="bi-heart" class="ir-text ir-text-secondary" v-show="!isHeartFilled"></v-icon>
        <v-icon name="bi-heart-fill" class="ir-text ir-text-secondary" v-show="isHeartFilled"></v-icon>
        <span class="ms-2 pt-2 ir-text ir-text-secondary">{{ likesCount }} likes</span>
      </span>
    </div>
    <hr class="border-emerald-950 border-y-2" />
  </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});

const comment = ref({});
comment.value = props.data;

let likesCount = ref(0);
likesCount.value = comment.value.comment_rating ?? 0;

let isHeartFilled = ref(false);
const handleLikeReact = () => {
  isHeartFilled.value = !isHeartFilled.value;
  isHeartFilled.value ? likesCount.value++ : likesCount.value--;
};
</script>
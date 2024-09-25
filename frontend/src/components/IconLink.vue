<template>
  <div class="flex items-center w-full">
    <v-icon :name="icon" scale="1.5" :fill="color" />
    <h1 class="ir-text-main ms-4">{{ link }}</h1>
  </div>
</template>

<script setup>
defineProps({
  icon: String,
  link: String,
  color: String,
});
</script>
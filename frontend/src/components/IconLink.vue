<template>
  <a :href="link" target="_blank" rel="noopener noreferrer" class="flex w-full items-center rounded px-2 py-1 hover:bg-white/10">
    <v-icon :name="icon" scale="1.5" :fill="color" />
    <span class="ir-text-main ms-4">{{ label }}</span>
  </a>
</template>

<script setup>
defineProps({
  icon: String,
  link: String,
  label: String,
  color: String,
});
</script>

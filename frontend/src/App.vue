<template>
  <FrontLayout v-if="layout === 'front'" />
  <AuthLayout v-else-if="layout === 'auth'" />
  <PortalLayout v-else-if="layout === 'portal'" />
</template>

<script setup>
import FrontLayout from "@/layout/Front.vue";
import AuthLayout from "@/layout/Auth.vue";
import PortalLayout from "@/layout/Portal.vue";
import { computed } from "vue";
import { useRoute } from "vue-router";

const route = useRoute();
const layout = computed(() => route.meta.layout);
</script>


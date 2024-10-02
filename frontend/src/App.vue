<template>
  <FrontLayout v-if="layout == 'front'" />
  <AuthLayout v-if="layout == 'auth'" />
  <PortalLayout v-if="layout == 'portal'" />
</template>

<script setup>
import FrontLayout from "@/layout/Front.vue";
import AuthLayout from "@/layout/Auth.vue";
import PortalLayout from "@/layout/Portal.vue";
import { watch, ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const layout = ref("");

watch(
  () => router.currentRoute.value,
  (to) => {
    layout.value = to.meta.layout;
  }
);
</script>


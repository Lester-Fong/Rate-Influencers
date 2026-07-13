<template>
  <div class="flex items-start justify-between flex-col w-full h-full">
    <div class="mt-5 mb-10 text-center w-full font-bold text-2xl">
      <h1 class="text-gray-400 ir-main_title text-2xl tracking-wider">Rate Influencers</h1>
      <hr class="mt-4 opacity-25 mx-4" />
    </div>
    <ul class="px-4 w-full">
      <li class="pb-3 cursor-pointer text-gray-500 font-medium hover:text-gray-300">
        <router-link :to="{ name: 'dashboard' }">Dashboard</router-link>
      </li>
      <li class="pb-3 cursor-pointer text-gray-500 font-medium hover:text-gray-300">
        <router-link :to="{ name: 'influencerPortal' }">Influencers</router-link>
      </li>
      <li class="pb-3 cursor-pointer text-gray-500 font-medium hover:text-gray-300">
        <router-link :to="{ name: 'reviewsPortal' }">Reviews</router-link>
      </li>
      <li class="pt-6">
        <button type="button" class="text-gray-500 font-medium hover:text-gray-300 disabled:opacity-60" :disabled="authStore.isLoading" @click="handleLogout">
          Sign out
        </button>
        <p v-if="authStore.errorMessage" class="mt-2 text-sm text-red-400" role="alert">{{ authStore.errorMessage }}</p>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const handleLogout = async () => {
  if (await authStore.logout()) {
    await router.replace({ name: "login" });
  }
};
</script>

<template>
  <div class="flex h-full min-h-screen flex-col p-4">
    <router-link :to="{ name: 'dashboard' }" class="mb-8 block px-2 pt-2" @click="emit('navigate')">
      <span class="block font-['Alfa_Slab_One'] text-xl uppercase tracking-wide">Rate</span>
      <span class="block font-['Lexend_Zetta'] text-sm uppercase text-rose-300">Influencers</span>
    </router-link>

    <nav class="space-y-1" aria-label="Administrator navigation">
      <router-link :to="{ name: 'dashboard' }" class="admin-nav-link" @click="emit('navigate')"><span aria-hidden="true">&#9672;</span> Dashboard</router-link>
      <router-link :to="{ name: 'adminInfluencers' }" class="admin-nav-link" @click="emit('navigate')"><span aria-hidden="true">&#9734;</span> Influencers</router-link>
      <router-link :to="{ name: 'adminReviews' }" class="admin-nav-link" @click="emit('navigate')"><span aria-hidden="true">&#10003;</span> Reviews</router-link>
    </nav>

    <div class="mt-auto border-t border-white/10 pt-4">
      <router-link :to="{ name: 'home' }" class="admin-nav-link mb-1" @click="emit('navigate')"><span aria-hidden="true">&#8592;</span> Public site</router-link>
      <button type="button" class="admin-nav-link w-full disabled:opacity-60" :disabled="authStore.isLoading" @click="handleLogout">
        <span aria-hidden="true">&#8617;</span> {{ authStore.isLoading ? "Signing out..." : "Sign out" }}
      </button>
      <p v-if="authStore.errorMessage" class="mt-2 px-3 text-sm text-red-300" role="alert">{{ authStore.errorMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const emit = defineEmits(["navigate"]);
const router = useRouter();
const authStore = useAuthStore();

const handleLogout = async () => {
  if (await authStore.logout()) {
    emit("navigate");
    await router.replace({ name: "login" });
  }
};
</script>

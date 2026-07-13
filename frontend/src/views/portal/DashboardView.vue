<template>
  <section>
    <div class="mb-7">
      <p class="mb-1 text-xs font-bold uppercase tracking-[0.2em] text-rose-500">Overview</p>
      <h1 class="admin-heading">Welcome back, {{ firstName }}</h1>
      <p class="admin-muted mt-1 text-sm">A quick snapshot of the RateInfluencers community.</p>
    </div>

    <div v-if="isLoading" class="admin-panel p-8 text-center admin-muted" role="status">Loading dashboard...</div>
    <div v-else-if="errorMessage" class="admin-panel border-red-300 p-5 text-red-600" role="alert">{{ errorMessage }}</div>

    <div v-else class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <article v-for="stat in stats" :key="stat.label" class="admin-panel relative overflow-hidden p-5">
        <span class="absolute -right-3 -top-5 text-7xl font-black text-rose-400/10" aria-hidden="true">{{ stat.symbol }}</span>
        <p class="admin-muted text-sm font-semibold">{{ stat.label }}</p>
        <p class="mt-2 text-3xl font-black">{{ stat.value }}</p>
        <p class="admin-muted mt-1 text-xs">{{ stat.note }}</p>
      </article>
    </div>

    <section class="admin-panel mt-6 p-5 sm:p-6">
      <h2 class="text-lg font-bold">Next actions</h2>
      <div class="mt-4 grid gap-3 sm:grid-cols-2">
        <router-link :to="{ name: 'adminReviews' }" class="rounded-xl bg-rose-400/10 p-4 font-semibold text-rose-600 hover:bg-rose-400/20">Moderate pending reviews</router-link>
        <router-link :to="{ name: 'adminInfluencers' }" class="rounded-xl bg-emerald-500/10 p-4 font-semibold text-emerald-700 hover:bg-emerald-500/20">Manage influencer profiles</router-link>
      </div>
    </section>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useInfluencerStore } from "@/stores/influencer";
import { useReviewStore } from "@/stores/review";

const authStore = useAuthStore();
const influencerStore = useInfluencerStore();
const reviewStore = useReviewStore();
const influencers = ref([]);
const reviews = ref([]);
const isLoading = ref(false);
const errorMessage = ref("");
const firstName = computed(() => (authStore.admin?.fullname || "Admin").split(" ")[0]);
const stats = computed(() => [
  { label: "Influencers", value: influencers.value.length, note: "Published profiles", symbol: "I" },
  { label: "Pending", value: reviews.value.filter((review) => review.status === "pending").length, note: "Awaiting moderation", symbol: "?" },
  { label: "Approved", value: reviews.value.filter((review) => review.status === "approved").length, note: "Visible publicly", symbol: "+" },
  { label: "Rejected", value: reviews.value.filter((review) => review.status === "rejected").length, note: "Excluded publicly", symbol: "-" },
]);

onMounted(async () => {
  isLoading.value = true;
  try {
    [influencers.value, reviews.value] = await Promise.all([
      influencerStore.getAdminInfluencers(),
      reviewStore.getAdminReviews(),
    ]);
  } catch (error) {
    errorMessage.value = error.response?.data?.message || "Unable to load dashboard data.";
  } finally {
    isLoading.value = false;
  }
});
</script>

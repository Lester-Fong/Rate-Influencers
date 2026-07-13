<template>
  <ReviewModal v-if="influencer" :open="isReviewModalOpen" :slug="slug" @close="isReviewModalOpen = false" @success="handleReviewSubmitted" />

  <main class="ir-page">
    <div v-if="isLoading" class="py-20 text-center ir-text ir-text-secondary" role="status">Loading influencer...</div>

    <div v-else-if="errorMessage" class="mx-auto max-w-2xl rounded-lg bg-red-700 p-6 text-center text-white" role="alert">
      <p>{{ errorMessage }}</p>
      <router-link :to="{ name: 'home' }" class="mt-3 inline-block font-semibold underline">Back to influencers</router-link>
    </div>

    <template v-else-if="influencer">
      <section class="ir-container ir-profile-panel grid gap-7 p-6 sm:p-8 lg:grid-cols-[auto_1fr_12rem] lg:items-center">
        <img
          v-if="influencer.profile_picture && !imageFailed"
          :src="influencer.profile_picture"
          :alt="`${influencer.name} profile`"
          class="ir-profile-avatar mx-auto lg:mx-0"
          @error="imageFailed = true"
        />
        <div v-else class="ir-profile-avatar mx-auto text-5xl font-bold lg:mx-0" aria-hidden="true">
          {{ influencer.name.charAt(0).toUpperCase() }}
        </div>

        <div class="ir-profile-copy min-w-0 text-center lg:text-left">
          <h1 class="ir-text-main text-3xl font-bold sm:text-4xl">{{ influencer.name }}</h1>
          <p v-if="influencer.bio" class="mt-3 max-w-full break-words text-base leading-relaxed ir-text-secondary">{{ influencer.bio }}</p>
          <div class="mt-4 flex max-w-full flex-wrap items-center justify-center gap-3 lg:justify-start">
            <star-rating
              :star-size="22"
              :increment="0.1"
              active-color="#a7d708"
              inactive-color="transparent"
              border-color="#F4978E"
              :border-width="1"
              :show-rating="false"
              :read-only="true"
              :display-only="true"
              :rating="Number(influencer.rating || 0)"
            />
            <span class="ir-text ir-text-secondary">{{ Number(influencer.rating || 0).toFixed(1) }} from {{ reviewLabel }}</span>
          </div>

          <div v-if="socialLinks.length" class="mt-5 grid gap-2 text-left sm:grid-cols-2">
            <IconLink v-for="social in socialLinks" :key="social.label" v-bind="social" />
          </div>
        </div>

        <button type="button" class="ir-review-button flex flex-col items-center justify-center gap-3 p-5 transition hover:-translate-y-1" @click="isReviewModalOpen = true">
          <span class="ir-text text-lg font-extrabold">Write a review</span>
          <v-icon name="oi-plus" scale="3" />
        </button>
      </section>

      <section class="ir-container mt-8 p-6 sm:p-8" aria-labelledby="reviews-title">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
          <h2 id="reviews-title" class="ir-text ir-text-main text-2xl">{{ reviewLabel }}</h2>
          <div v-if="reviews.length > 1" class="flex gap-2" aria-label="Review order">
            <button type="button" class="ir-text ir-text-secondary" :class="{ underline: sortOrder === 'latest' }" @click="sortOrder = 'latest'">Latest</button>
            <span class="ir-text-secondary">/</span>
            <button type="button" class="ir-text ir-text-secondary" :class="{ underline: sortOrder === 'top' }" @click="sortOrder = 'top'">Top rated</button>
          </div>
        </div>

        <p v-if="reviews.length === 0" class="py-8 text-center ir-text ir-text-secondary">No approved reviews yet. Be the first to submit one.</p>
        <Review v-for="review in sortedReviews" v-else :key="review.id" :review="review" />
      </section>
    </template>
  </main>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { useRoute } from "vue-router";
import IconLink from "@/components/IconLink.vue";
import Review from "@/components/Review.vue";
import ReviewModal from "@/components/ReviewModal.vue";
import { useInfluencerStore } from "@/stores/influencer";

const route = useRoute();
const influencerStore = useInfluencerStore();
const influencer = ref(null);
const isLoading = ref(false);
const errorMessage = ref("");
const sortOrder = ref("latest");
const isReviewModalOpen = ref(false);
const imageFailed = ref(false);
const slug = computed(() => String(route.params.influencerSlug || ""));
const reviews = computed(() => influencer.value?.reviews || []);
const reviewLabel = computed(() => {
  const count = Number(influencer.value?.review_count || 0);
  return `${count} ${count === 1 ? "review" : "reviews"}`;
});
const sortedReviews = computed(() => {
  const items = [...reviews.value];

  if (sortOrder.value === "top") {
    return items.sort((a, b) => b.rating - a.rating);
  }

  return items.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});
const socialLinks = computed(() =>
  [
    { label: "Facebook", link: influencer.value?.facebook_link, icon: "bi-facebook", color: "#1877f2" },
    { label: "Instagram", link: influencer.value?.instagram_link, icon: "bi-instagram", color: "#e1306c" },
    { label: "TikTok", link: influencer.value?.tiktok_link, icon: "bi-tiktok", color: "#ffffff" },
    { label: "YouTube", link: influencer.value?.youtube_link, icon: "bi-youtube", color: "#ff0000" },
  ].filter((social) => social.link)
);

const loadInfluencer = async () => {
  isLoading.value = true;
  errorMessage.value = "";
  influencer.value = null;
  imageFailed.value = false;

  try {
    influencer.value = await influencerStore.showInfluencer(slug.value);
  } catch (error) {
    errorMessage.value = error.response?.data?.message || "Unable to load this influencer.";
  } finally {
    isLoading.value = false;
  }
};

const handleReviewSubmitted = async () => {
  isReviewModalOpen.value = false;
  await loadInfluencer();
};

watch(slug, loadInfluencer, { immediate: true });
</script>

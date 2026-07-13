<template>
  <section class="mx-auto w-11/12 pb-16 lg:w-10/12">
    <div class="ir-container-subtitle mx-auto mb-10 w-full rounded-none border p-6 lg:w-8/12">
      <h1 class="ir-text ir-text-main text-center text-2xl font-bold">
        Unmasking the truth behind the filters: real opinions on the digital creators you follow.
      </h1>
    </div>

    <form class="mx-auto mb-10 flex max-w-2xl gap-3" role="search" @submit.prevent="searchInfluencers">
      <label for="influencer-search" class="sr-only">Search influencers</label>
      <input
        id="influencer-search"
        v-model.trim="searchTerm"
        type="search"
        maxlength="100"
        class="min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-800 focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-600/30"
        placeholder="Search by influencer name"
      />
      <button type="submit" class="rounded-lg bg-emerald-800 px-5 py-3 font-semibold text-white hover:bg-emerald-700" :disabled="isLoading">
        Search
      </button>
      <button v-if="activeSearch" type="button" class="rounded-lg border border-gray-400 px-4 py-3 text-gray-200 hover:bg-white/10" @click="clearSearch">
        Clear
      </button>
    </form>

    <div v-if="isLoading" class="py-16 text-center ir-text ir-text-secondary">Loading influencers…</div>

    <div v-else-if="errorMessage" class="mx-auto max-w-2xl rounded-lg bg-red-700 p-6 text-center text-white" role="alert">
      <p>{{ errorMessage }}</p>
      <button type="button" class="mt-3 font-semibold underline" @click="loadInfluencers(activeSearch)">Try again</button>
    </div>

    <div v-else-if="influencers.length === 0" class="py-16 text-center">
      <h2 class="ir-text ir-text-main text-2xl">{{ activeSearch ? "No matching influencers" : "No influencers yet" }}</h2>
      <p class="mt-2 ir-text ir-text-secondary">
        {{ activeSearch ? `Try a different search than “${activeSearch}”.` : "Check back after profiles have been added." }}
      </p>
    </div>

    <div v-else class="grid grid-cols-1 gap-12 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
      <InfluencerCard
        v-for="influencer in influencers"
        :key="influencer.id"
        :data="influencer"
        @click="openInfluencer(influencer.slug)"
      />
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import InfluencerCard from "@/components/InfluencerCard.vue";
import { useInfluencerStore } from "@/stores/influencer";

const router = useRouter();
const influencerStore = useInfluencerStore();
const influencers = ref([]);
const searchTerm = ref("");
const activeSearch = ref("");
const isLoading = ref(false);
const errorMessage = ref("");

const loadInfluencers = async (search = "") => {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    influencers.value = await influencerStore.getInfluencers(search);
  } catch (error) {
    errorMessage.value = error.response?.data?.message || "Unable to load influencers.";
  } finally {
    isLoading.value = false;
  }
};

const searchInfluencers = async () => {
  activeSearch.value = searchTerm.value;
  await loadInfluencers(activeSearch.value);
};

const clearSearch = async () => {
  searchTerm.value = "";
  activeSearch.value = "";
  await loadInfluencers();
};

const openInfluencer = (influencerSlug) => {
  router.push({ name: "influencer", params: { influencerSlug } });
};

onMounted(() => loadInfluencers());
</script>

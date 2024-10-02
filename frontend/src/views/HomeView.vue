<template>
  <div class="ir-loader" v-if="isLoading"></div>
  <div class="ir-container-subtitle w-7/12 mx-auto rounded-none border mb-5 lg:mb-20">
    <h2 class="ir-text ir-text-main font-bold text-2xl text-center">“ Unmasking the truth behind the filters: Real opinions on the digital stars you follow. ”</h2>
  </div>

  <div class="w-7/12 mx-auto rounded-md p-10 bg-red-600 text-xl" v-if="error_message">
    <h3 class="text-teal-100 text-center font-semibold">{{ error_message }}</h3>
  </div>

  <div class="w-10/12 mx-auto p-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 md:grid-cols-2 xl:grid-cols-4 gap-12">
      <InfluencerCard v-for="(data, i) in influencers" :data="data" :key="i" @click="onRedirectToInfluencerPage(data.slug)" />
    </div>
  </div>
</template>


<script setup>
import InfluencerCard from "@/components/InfluencerCard.vue";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { useInfluencerStore } from "../stores/influencer";

const router = useRouter();
const isLoading = ref(false);
const influencers = ref([]);
const influencerStore = useInfluencerStore();
const error_message = ref("");

const onRedirectToInfluencerPage = (influencerSlug) => {
  router.push({ name: "influencer", params: { influencerSlug } });
};

onMounted(() => {
  onPopulateInfluencers();
});

const onPopulateInfluencers = async () => {
  isLoading.value = true;
  const response = await influencerStore.getInfluencers();
  isLoading.value = false;

  if (response.error) {
    error_message.value = "No influencers found";
    console.log("Error: ", response.error);

    return;
  }

  influencers.value = await response;
};
</script>
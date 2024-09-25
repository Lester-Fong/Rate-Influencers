<template>
  <div class="ir-loader" v-if="isLoading"></div>
  <ReviewModal :slug="slug" @success="onSuccess" />
  <div class="px-4 lg:px-0">
    <div
      class="ir-container flex justify-center mt-3 items-center pb-5 w-full lg:w-6/12 mx-auto"
    >
      <img
        src="https://picsum.photos/500/500"
        class="rounded-full ir-avatar left-10"
        alt="img-1"
      />
      <div class="grid grid-cols-4 w-full">
        <div class="flex items-center mt-20 flex-col">
          <h1 class="ir-text-main ir-text-main_title">
            {{ influencer?.name }}
          </h1>
          <star-rating
            :star-size="20"
            :increment="0.5"
            active-color="#a7d708"
            inactive-color="transparent"
            border-color="#F4978E"
            :border-width="1"
            :show-rating="false"
            :read-only="true"
            :display-only="true"
            :rating="influencer?.rating"
          >
          </star-rating>
          <p class="ir-text ir-text-secondary text-center text-sm mt-2">
            12,345 ratings
          </p>
        </div>
        <div class="col-span-2 pt-4">
          <div class="flex justify-center items-center flex-col gap-y-2">
            <IconLink
              icon="bi-facebook"
              link="https://www.facebook.com/Dance.Fonggi"
              color="blue"
            />
            <IconLink
              icon="bi-instagram"
              link="https://www.instagram.com/@fonggi_"
              color="orange"
            />
            <IconLink
              icon="bi-tiktok"
              link="https://www.tiktok.com/@Dance.Fonggi"
              color="black"
            />
            <IconLink
              icon="bi-youtube"
              link="https://www.youtube.com/c/DanceFonggi"
              color="red"
            />
          </div>
        </div>
        <div class="flex flex-col p-3">
          <button
            class="ir-review-button opacity-80 hover:scale-105 hover:opacity-100 transition-all duration-200"
            @click="onShowReviewModal"
            type="button"
            id="button"
          >
            <h5 class="ir-text font-extrabold text-lg">Write a Review</h5>
            <v-icon name="oi-plus" scale="4" />
          </button>
          <!-- Modal toggle -->
        </div>
      </div>
    </div>

    <!-- Reviews and Other Influencers -->
    <div
      class="w-10/12 py-20 gap-x-20 mx-auto flex justify-between items-start"
    >
      <div class="ir-container basis-8/12 p-5">
        <div class="flex justify-between items-end pb-5">
          <h1 class="ir-text text-2xl ir-text-main">197 Reviews</h1>
          <h1 class="ir-text text-2xl ir-text-secondary">
            <span class="cursor-pointer opacity-90 hover:opacity-100"
              >Latest</span
            >
            /
            <span class="cursor-pointer opacity-90 hover:opacity-100">Top</span>
          </h1>
        </div>
        <Comment v-for="(data, i) in comments" :data="data" />
      </div>
      <div
        class="ir-container basis-3/12 pt-5 pb-10 px-8 bg-neutral-800 drop-shadow-2xl drop-shadow-neutral-200"
      >
        <h1 class="ir-text text-2xl ir-text-main text-center">
          Other Influencers
        </h1>
        <InfluencerCard
          class="mt-20"
          v-for="(data, i) in otherInfluencers"
          :key="i"
          :data="data"
          @click="onRedirectToInfluencerPage(data.slug)"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { Modal } from "flowbite";
import IconLink from "@/components/IconLink.vue";
import ReviewModal from "@/components/ReviewModal.vue";
import Comment from "@/components/Comment.vue";
import InfluencerCard from "@/components/InfluencerCard.vue";
import { useInfluencerStore } from "../stores/influencer";

const comments = ref([]);
const influencer = ref();
const otherInfluencers = ref([]);
const route = useRoute();
const router = useRouter();
const influencerStore = useInfluencerStore();
let slug = "";
const isLoading = ref(false);

onMounted(() => {
  slug = route.params.influencerSlug;
  onPopulateInfluencerData(slug);
});

const onPopulateInfluencerData = async (slug) => {
  isLoading.value = true;
  handleResetData();
  const response = await influencerStore.showInfluencer(slug);
  influencer.value = response[0];
  comments.value = influencer.value.comments;
  otherInfluencers.value = response[1];
  isLoading.value = false;
};

const onRedirectToInfluencerPage = (influencerSlug) => {
  router.push({ name: "influencer", params: { influencerSlug } });
  onPopulateInfluencerData(influencerSlug);
};

const onShowReviewModal = () => {
  const $modalElement = document.querySelector("#modal");
  if ($modalElement) {
    const modal = new Modal($modalElement, { closable: false });
    modal.show();
  }
};

const onSuccess = () => {
  const $modalElement = document.querySelector("#modal");
  if ($modalElement) {
    const modal = new Modal($modalElement);
    modal.hide();
    onPopulateInfluencerData(slug);
  }
};

const handleResetData = () => {
  influencer.value = {};
  otherInfluencers.value = [];
  comments.value = [];
};
</script>
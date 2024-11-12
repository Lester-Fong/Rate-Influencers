<template>
  <div class="ir-loader" v-if="isLoading"></div>
  <div id="influencer_modal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden rounded-lg w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-2xl max-h-full">
      <!-- Modal content -->
      <div class="relative bg-slate-700 rounded-lg shadow">
        <form class="p-6 space-y-6 text-white" @submit.prevent="handleSubmit">
          <h3 class="text-3xl font-bold ir-text text-center">Add a Review</h3>
          <div class="flex justify-between flex-col items-center gap-y-5">
            <div class="w-full">
              <label for="name" class="ir-text block mb-2 text-xl font-medium">Name: </label>
              <input
                type="text"
                class="ir-text block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                v-model="name"
              />
              <span class="text-red-500 ir-text text-sm">{{ name_error }}</span>
            </div>
            <div class="w-full">
              <label for="name" class="ir-text block mb-2 text-xl font-medium">Facebook Link:</label>
              <input
                type="text"
                class="ir-text block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                v-model="fbLink"
              />
              <span class="text-red-500 ir-text text-sm">{{ fbLink_error }}</span>
            </div>
            <div class="w-full">
              <label for="name" class="ir-text block mb-2 text-xl font-medium">Youtube Link:</label>
              <input
                type="text"
                class="ir-text block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                v-model="ytLink"
              />
              <span class="text-red-500 ir-text text-sm">{{ ytLink_error }}</span>
            </div>
            <div class="w-full">
              <label for="name" class="ir-text block mb-2 text-xl font-medium">Instagram Link:</label>
              <input
                type="text"
                class="ir-text block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                v-model="igLink"
              />
              <span class="text-red-500 ir-text text-sm">{{ igLink_error }}</span>
            </div>
            <div class="w-full">
              <label for="name" class="ir-text block mb-2 text-xl font-medium">Tiktok Link:</label>
              <input
                type="text"
                class="ir-text block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                v-model="tiktokLink"
              />
              <span class="text-red-500 ir-text text-sm">{{ tiktokLink_error }}</span>
            </div>
          </div>
          <div class="flex justify-end space-x-2 rounded-b">
            <button
              type="button"
              class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
              @click="onCloseModal"
            >
              Cancel
            </button>
            <button class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="submit">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
  
  <script setup>
import Swal from "sweetalert2";
import { Modal } from "flowbite";
import { ref } from "vue";
import { useInfluencerStore } from "../stores/influencer";

const isLoading = ref(false);
const name = ref("");
const name_error = ref("");
const fbLink = ref("");
const fbLink_error = ref("");
const ytLink = ref("");
const ytLink_error = ref("");
const igLink = ref("");
const igLink_error = ref("");
const tiktokLink = ref("");
const tiktokLink_error = ref("");

const influencerStore = useInfluencerStore();
const emit = defineEmits(["success"]);

const props = defineProps(["slug"]);

const onCloseModal = () => {
  const $modalElement = document.querySelector("#influencer_modal");
  if ($modalElement) {
    const modal = new Modal($modalElement);
    resetForm();
    modal.hide();
  }
};

const handleSubmit = async () => {
  if (passedValidation()) {
    isLoading.value = true;
    const response = await influencerStore.addInfluencerComment({
      name: name.value,
      influencer_rating: rating.value,
      comment: comment.value,
      slug: props.slug,
    });

    if (response.error) {
      Swal.fire("Error", response.message, "error");
    } else {
      Swal.fire("Success", response.message, "success");
    }
    isLoading.value = false;
    resetForm();
    emit("success");
  }
};

const resetForm = () => {
  name.value = "";
  rating.value = 0;
  comment.value = "";
};

const passedValidation = () => {
  let is_valid = true;
  if (!name.value) {
    name_error.value = "Name is required";
    is_valid = false;
  } else {
    name_error.value = "";
  }
  if (!rating.value || rating.value === 0) {
    rating_error.value = "Rating is required";
    is_valid = false;
  } else {
    rating_error.value = "";
  }

  if (!comment.value) {
    comment_error.value = "Comment is required";
    is_valid = false;
  } else {
    comment_error.value = "";
  }
  return is_valid;
};
</script>
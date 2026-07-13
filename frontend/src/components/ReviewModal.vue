<template>
  <div
    v-if="open"
    class="fixed inset-0 z-50 grid place-items-center overflow-y-auto bg-black/70 p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="review-modal-title"
    @click.self="closeModal"
    @keydown.esc="closeModal"
  >
    <form class="ir-container w-full max-w-xl p-6 sm:p-8" @submit.prevent="handleSubmit">
      <div class="mb-6 flex items-center justify-between gap-4">
        <h2 id="review-modal-title" class="ir-text-main text-2xl font-bold">Write a review</h2>
        <button type="button" class="rounded-full p-2 text-2xl text-rose-200 hover:bg-white/10" :disabled="isLoading" aria-label="Close review form" @click="closeModal">&times;</button>
      </div>

      <div class="space-y-5">
        <div>
          <label for="reviewer-name" class="mb-2 block font-semibold text-rose-100">Your name</label>
          <input id="reviewer-name" ref="nameInput" v-model.trim="form.reviewer_name" class="w-full rounded-xl border border-white/20 bg-white px-4 py-3 text-gray-900" type="text" maxlength="50" autocomplete="name" />
          <p v-if="fieldError('reviewer_name')" class="mt-1 text-sm text-red-300">{{ fieldError("reviewer_name") }}</p>
        </div>

        <fieldset>
          <legend class="mb-2 font-semibold text-rose-100">Rating</legend>
          <star-rating :star-size="32" :increment="1" active-color="#a7d708" inactive-color="transparent" border-color="#f4978e" :border-width="2" :show-rating="false" v-model:rating="form.rating" />
          <p v-if="fieldError('rating')" class="mt-1 text-sm text-red-300">{{ fieldError("rating") }}</p>
        </fieldset>

        <div>
          <label for="review-message" class="mb-2 block font-semibold text-rose-100">Your review</label>
          <textarea id="review-message" v-model.trim="form.review" class="min-h-32 w-full resize-y rounded-xl border border-white/20 bg-white px-4 py-3 text-gray-900" maxlength="500" placeholder="Share your thoughts about this creator's public content."></textarea>
          <div class="mt-1 flex justify-between gap-3 text-sm">
            <p v-if="fieldError('review')" class="text-red-300">{{ fieldError("review") }}</p>
            <span class="ml-auto text-rose-200/70">{{ form.review.length }}/500</span>
          </div>
        </div>

        <p v-if="errorMessage" class="rounded-lg bg-red-950/60 p-3 text-sm text-red-200" role="alert">{{ errorMessage }}</p>
      </div>

      <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <button type="button" class="rounded-xl border border-white/25 px-5 py-3 text-rose-100 hover:bg-white/10" :disabled="isLoading" @click="closeModal">Cancel</button>
        <button class="ir-button-primary px-6 py-3 disabled:cursor-not-allowed disabled:opacity-60" type="submit" :disabled="isLoading">
          {{ isLoading ? "Submitting..." : "Submit for approval" }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import Swal from "sweetalert2";
import { nextTick, reactive, ref, watch } from "vue";
import { useReviewStore } from "@/stores/review";

const props = defineProps({
  open: Boolean,
  slug: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(["close", "success"]);
const reviewStore = useReviewStore();
const nameInput = ref(null);
const isLoading = ref(false);
const errors = ref({});
const errorMessage = ref("");
const form = reactive({ reviewer_name: "", rating: 0, review: "" });

watch(() => props.open, async (open) => {
  if (open) {
    await nextTick();
    nameInput.value?.focus();
  }
});

const resetForm = () => {
  form.reviewer_name = "";
  form.rating = 0;
  form.review = "";
  errors.value = {};
  errorMessage.value = "";
};

const closeModal = () => {
  if (isLoading.value) return;
  resetForm();
  emit("close");
};

const validate = () => {
  const nextErrors = {};
  if (!form.reviewer_name) nextErrors.reviewer_name = ["Name is required."];
  if (!Number.isInteger(form.rating) || form.rating < 1 || form.rating > 5) nextErrors.rating = ["Choose a whole-star rating from 1 to 5."];
  if (!form.review) nextErrors.review = ["Review is required."];
  errors.value = nextErrors;
  return Object.keys(nextErrors).length === 0;
};

const handleSubmit = async () => {
  if (!validate()) return;

  isLoading.value = true;
  errors.value = {};
  errorMessage.value = "";

  try {
    const response = await reviewStore.submitReview(props.slug, { ...form });
    isLoading.value = false;
    resetForm();
    emit("success");
    await Swal.fire("Review submitted", response.message, "success");
  } catch (error) {
    errors.value = error.response?.data?.errors || {};
    errorMessage.value = error.response?.data?.message || "Unable to submit the review.";
  } finally {
    isLoading.value = false;
  }
};

const fieldError = (field) => errors.value[field]?.[0] || "";
</script>

<template>
  <section>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-700">Review moderation</h1>
        <p class="mt-1 text-sm text-gray-500">Approve reviews for public display or reject them.</p>
      </div>
      <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50" :disabled="isLoading" @click="loadReviews">
        Refresh
      </button>
    </div>

    <div class="mb-5 flex flex-wrap gap-2" aria-label="Filter reviews by status">
      <button
        v-for="option in filters"
        :key="option.value"
        type="button"
        class="rounded-full px-4 py-2 text-sm font-semibold"
        :class="activeFilter === option.value ? 'bg-emerald-800 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
        @click="activeFilter = option.value"
      >
        {{ option.label }} ({{ statusCount(option.value) }})
      </button>
    </div>

    <div v-if="isLoading" class="rounded-lg bg-white p-10 text-center text-gray-500">Loading reviews…</div>

    <div v-else-if="errorMessage" class="rounded-lg bg-red-50 p-6 text-red-700" role="alert">
      <p>{{ errorMessage }}</p>
      <button type="button" class="mt-2 font-semibold underline" @click="loadReviews">Try again</button>
    </div>

    <div v-else-if="filteredReviews.length === 0" class="rounded-lg bg-white p-10 text-center text-gray-500">
      No {{ activeFilter === "all" ? "" : `${activeFilter} ` }}reviews found.
    </div>

    <div v-else class="overflow-x-auto rounded-lg bg-white shadow">
      <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-500">
          <tr>
            <th class="px-5 py-3">Reviewer</th>
            <th class="px-5 py-3">Influencer</th>
            <th class="px-5 py-3">Rating</th>
            <th class="px-5 py-3">Review</th>
            <th class="px-5 py-3">Status</th>
            <th class="px-5 py-3">Submitted</th>
            <th class="px-5 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
          <tr v-for="review in filteredReviews" :key="review.id" class="align-top">
            <td class="whitespace-nowrap px-5 py-4 font-medium">{{ review.reviewer_name }}</td>
            <td class="whitespace-nowrap px-5 py-4">{{ review.influencer?.name || "Unknown" }}</td>
            <td class="whitespace-nowrap px-5 py-4">{{ review.rating }}/5</td>
            <td class="max-w-md px-5 py-4">{{ review.review }}</td>
            <td class="px-5 py-4">
              <span class="rounded-full px-3 py-1 text-xs font-semibold capitalize" :class="statusClass(review.status)">{{ review.status }}</span>
            </td>
            <td class="whitespace-nowrap px-5 py-4">{{ formatDate(review.created_at) }}</td>
            <td class="whitespace-nowrap px-5 py-4 text-right">
              <button
                v-if="review.status !== 'approved'"
                type="button"
                class="mr-3 font-semibold text-emerald-700 hover:text-emerald-900 disabled:opacity-50"
                :disabled="moderatingId === review.id"
                @click="moderate(review, 'approved')"
              >
                Approve
              </button>
              <button
                v-if="review.status !== 'rejected'"
                type="button"
                class="font-semibold text-red-600 hover:text-red-800 disabled:opacity-50"
                :disabled="moderatingId === review.id"
                @click="moderate(review, 'rejected')"
              >
                Reject
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import Swal from "sweetalert2";
import { useReviewStore } from "@/stores/review";

const reviewStore = useReviewStore();
const reviews = ref([]);
const activeFilter = ref("pending");
const isLoading = ref(false);
const errorMessage = ref("");
const moderatingId = ref(null);
const filters = [
  { label: "Pending", value: "pending" },
  { label: "Approved", value: "approved" },
  { label: "Rejected", value: "rejected" },
  { label: "All", value: "all" },
];

const filteredReviews = computed(() =>
  activeFilter.value === "all"
    ? reviews.value
    : reviews.value.filter((review) => review.status === activeFilter.value)
);

const loadReviews = async () => {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    reviews.value = await reviewStore.getAdminReviews();
  } catch (error) {
    errorMessage.value = error.response?.data?.message || "Unable to load reviews.";
  } finally {
    isLoading.value = false;
  }
};

const moderate = async (review, status) => {
  const action = status === "approved" ? "approve" : "reject";
  const confirmation = await Swal.fire({
    title: `${action.charAt(0).toUpperCase()}${action.slice(1)} this review?`,
    text: "The influencer rating and review count will be recalculated.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: action.charAt(0).toUpperCase() + action.slice(1),
  });

  if (!confirmation.isConfirmed) return;

  moderatingId.value = review.id;

  try {
    const updated = status === "approved"
      ? await reviewStore.approveReview(review.id)
      : await reviewStore.rejectReview(review.id);
    const index = reviews.value.findIndex((item) => item.id === review.id);
    if (index !== -1) reviews.value[index] = updated;
    await Swal.fire("Updated", `The review is now ${status}.`, "success");
  } catch (error) {
    await Swal.fire("Error", error.response?.data?.message || "Unable to moderate the review.", "error");
  } finally {
    moderatingId.value = null;
  }
};

const statusCount = (status) =>
  status === "all" ? reviews.value.length : reviews.value.filter((review) => review.status === status).length;

const statusClass = (status) => ({
  "bg-amber-100 text-amber-800": status === "pending",
  "bg-emerald-100 text-emerald-800": status === "approved",
  "bg-red-100 text-red-800": status === "rejected",
});

const formatDate = (value) => value ? new Intl.DateTimeFormat(undefined, { dateStyle: "medium" }).format(new Date(value)) : "—";

onMounted(loadReviews);
</script>

<template>
  <section>
    <InfluencerModal
      :open="isModalOpen"
      :influencer="selectedInfluencer"
      @close="closeModal"
      @saved="handleSaved"
    />

    <div class="mb-6 flex items-center justify-between gap-4">
      <div>
        <h1 class="ir-text text-xl font-bold text-gray-700">Influencers</h1>
        <p class="mt-1 text-sm text-gray-500">Create and maintain influencer profiles.</p>
      </div>
      <button
        type="button"
        class="rounded-lg bg-rose-800 px-5 py-2 text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500"
        @click="openCreateModal"
      >
        Create influencer
      </button>
    </div>

    <div v-if="isLoading" class="rounded-lg bg-white p-8 text-center text-gray-500">
      Loading influencers…
    </div>

    <div v-else-if="errorMessage" class="rounded-lg border border-red-200 bg-red-50 p-5 text-red-700" role="alert">
      <p>{{ errorMessage }}</p>
      <button type="button" class="mt-3 font-semibold underline" @click="loadInfluencers">Try again</button>
    </div>

    <div v-else-if="influencers.length === 0" class="rounded-lg border border-dashed border-gray-300 bg-white p-10 text-center">
      <h2 class="font-semibold text-gray-700">No influencers yet</h2>
      <p class="mt-2 text-sm text-gray-500">Create the first influencer profile to populate the public grid.</p>
    </div>

    <div v-else class="overflow-x-auto rounded-lg bg-white shadow">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Influencer</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Slug</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Rating</th>
            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Reviews</th>
            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="influencer in influencers" :key="influencer.id">
            <td class="px-5 py-4">
              <div class="flex items-center gap-3">
                <img
                  v-if="influencer.profile_picture"
                  :src="influencer.profile_picture"
                  :alt="`${influencer.name} profile`"
                  class="h-10 w-10 rounded-full object-cover"
                />
                <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 font-semibold text-gray-600">
                  {{ influencer.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <p class="font-medium text-gray-800">{{ influencer.name }}</p>
                  <p class="max-w-xs truncate text-sm text-gray-500">{{ influencer.bio || "No bio" }}</p>
                </div>
              </div>
            </td>
            <td class="px-5 py-4 text-sm text-gray-600">{{ influencer.slug }}</td>
            <td class="px-5 py-4 text-sm text-gray-600">{{ formatRating(influencer.rating) }}</td>
            <td class="px-5 py-4 text-sm text-gray-600">{{ influencer.review_count }}</td>
            <td class="px-5 py-4 text-right">
              <button type="button" class="mr-4 font-medium text-blue-700 hover:text-blue-900" @click="openEditModal(influencer)">Edit</button>
              <button
                type="button"
                class="font-medium text-red-700 hover:text-red-900 disabled:opacity-50"
                :disabled="deletingId === influencer.id"
                @click="deleteInfluencer(influencer)"
              >
                {{ deletingId === influencer.id ? "Deleting…" : "Delete" }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script setup>
import Swal from "sweetalert2";
import { onMounted, ref } from "vue";
import InfluencerModal from "@/components/InfluencerModal.vue";
import { useInfluencerStore } from "@/stores/influencer";

const influencerStore = useInfluencerStore();
const influencers = ref([]);
const isLoading = ref(false);
const errorMessage = ref("");
const isModalOpen = ref(false);
const selectedInfluencer = ref(null);
const deletingId = ref(null);

const loadInfluencers = async () => {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    influencers.value = await influencerStore.getAdminInfluencers();
  } catch (error) {
    errorMessage.value = error.response?.data?.message || "Unable to load influencers.";
  } finally {
    isLoading.value = false;
  }
};

const openCreateModal = () => {
  selectedInfluencer.value = null;
  isModalOpen.value = true;
};

const openEditModal = (influencer) => {
  selectedInfluencer.value = influencer;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedInfluencer.value = null;
};

const handleSaved = async () => {
  closeModal();
  await loadInfluencers();
};

const deleteInfluencer = async (influencer) => {
  const result = await Swal.fire({
    title: `Delete ${influencer.name}?`,
    text: "This also deletes all reviews attached to this influencer.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Delete",
    confirmButtonColor: "#b91c1c",
  });

  if (!result.isConfirmed) {
    return;
  }

  deletingId.value = influencer.id;

  try {
    await influencerStore.deleteInfluencer(influencer.id);
    influencers.value = influencers.value.filter((item) => item.id !== influencer.id);
    await Swal.fire("Deleted", "The influencer was deleted.", "success");
  } catch (error) {
    await Swal.fire("Unable to delete", error.response?.data?.message || "Please try again.", "error");
  } finally {
    deletingId.value = null;
  }
};

const formatRating = (rating) => Number(rating || 0).toFixed(1);

onMounted(loadInfluencers);
</script>

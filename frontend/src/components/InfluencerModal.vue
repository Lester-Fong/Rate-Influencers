<template>
  <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true" :aria-labelledby="titleId">
    <div class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-xl bg-white shadow-2xl">
      <form @submit.prevent="handleSubmit">
        <div class="flex items-center justify-between border-b px-6 py-4">
          <h2 :id="titleId" class="text-xl font-semibold text-gray-800">
            {{ isEditing ? "Edit influencer" : "Create influencer" }}
          </h2>
          <button type="button" class="text-2xl text-gray-500 hover:text-gray-800" :disabled="isLoading" aria-label="Close" @click="emit('close')">&times;</button>
        </div>

        <div class="grid gap-5 p-6 md:grid-cols-2">
          <div>
            <label for="influencer-name" class="mb-1 block text-sm font-medium text-gray-700">Name</label>
            <input id="influencer-name" v-model.trim="form.name" class="w-full rounded-lg border px-3 py-2" type="text" @input="setSuggestedSlug" />
            <p v-if="fieldError('name')" class="mt-1 text-sm text-red-600">{{ fieldError("name") }}</p>
          </div>

          <div>
            <label for="influencer-slug" class="mb-1 block text-sm font-medium text-gray-700">Slug</label>
            <input id="influencer-slug" v-model.trim="form.slug" class="w-full rounded-lg border px-3 py-2" type="text" placeholder="example-creator" />
            <p v-if="fieldError('slug')" class="mt-1 text-sm text-red-600">{{ fieldError("slug") }}</p>
          </div>

          <div class="md:col-span-2">
            <label for="influencer-bio" class="mb-1 block text-sm font-medium text-gray-700">Bio</label>
            <textarea id="influencer-bio" v-model.trim="form.bio" class="min-h-24 w-full rounded-lg border px-3 py-2" maxlength="2000"></textarea>
            <p v-if="fieldError('bio')" class="mt-1 text-sm text-red-600">{{ fieldError("bio") }}</p>
          </div>

          <div class="md:col-span-2">
            <label for="profile-picture" class="mb-1 block text-sm font-medium text-gray-700">Profile picture URL</label>
            <input id="profile-picture" v-model.trim="form.profile_picture" class="w-full rounded-lg border px-3 py-2" type="url" placeholder="https://…" />
            <p v-if="fieldError('profile_picture')" class="mt-1 text-sm text-red-600">{{ fieldError("profile_picture") }}</p>
          </div>

          <div v-for="field in socialFields" :key="field.key">
            <label :for="field.key" class="mb-1 block text-sm font-medium text-gray-700">{{ field.label }}</label>
            <input :id="field.key" v-model.trim="form[field.key]" class="w-full rounded-lg border px-3 py-2" type="url" placeholder="https://…" />
            <p v-if="fieldError(field.key)" class="mt-1 text-sm text-red-600">{{ fieldError(field.key) }}</p>
          </div>

          <p v-if="errorMessage" class="md:col-span-2 rounded-lg bg-red-50 p-3 text-sm text-red-700" role="alert">{{ errorMessage }}</p>
        </div>

        <div class="flex justify-end gap-3 border-t px-6 py-4">
          <button type="button" class="rounded-lg border px-4 py-2 text-gray-700 hover:bg-gray-50" :disabled="isLoading" @click="emit('close')">Cancel</button>
          <button type="submit" class="rounded-lg bg-rose-800 px-5 py-2 text-white hover:bg-rose-700 disabled:opacity-60" :disabled="isLoading">
            {{ isLoading ? "Saving…" : isEditing ? "Save changes" : "Create influencer" }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from "vue";
import { useInfluencerStore } from "@/stores/influencer";

const props = defineProps({
  open: {
    type: Boolean,
    required: true,
  },
  influencer: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits(["close", "saved"]);
const influencerStore = useInfluencerStore();
const isLoading = ref(false);
const errors = ref({});
const errorMessage = ref("");
const titleId = "influencer-modal-title";

const emptyForm = () => ({
  name: "",
  slug: "",
  bio: "",
  profile_picture: "",
  facebook_link: "",
  youtube_link: "",
  tiktok_link: "",
  instagram_link: "",
});

const form = reactive(emptyForm());
const isEditing = computed(() => Boolean(props.influencer?.id));
const socialFields = [
  { key: "facebook_link", label: "Facebook URL" },
  { key: "youtube_link", label: "YouTube URL" },
  { key: "tiktok_link", label: "TikTok URL" },
  { key: "instagram_link", label: "Instagram URL" },
];

const resetForm = () => {
  const values = emptyForm();

  for (const key of Object.keys(values)) {
    values[key] = props.influencer?.[key] || "";
  }

  Object.assign(form, values);
  errors.value = {};
  errorMessage.value = "";
};

watch(
  () => [props.open, props.influencer],
  ([open]) => {
    if (open) {
      resetForm();
    }
  }
);

const setSuggestedSlug = () => {
  if (!isEditing.value) {
    form.slug = form.name
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-|-$/g, "");
  }
};

const payload = () =>
  Object.fromEntries(
    Object.entries(form).map(([key, value]) => [key, value === "" ? null : value])
  );

const handleSubmit = async () => {
  isLoading.value = true;
  errors.value = {};
  errorMessage.value = "";

  try {
    if (isEditing.value) {
      await influencerStore.updateInfluencer(props.influencer.id, payload());
    } else {
      await influencerStore.createInfluencer(payload());
    }

    emit("saved");
  } catch (error) {
    errors.value = error.response?.data?.errors || {};
    errorMessage.value = error.response?.data?.message || "Unable to save the influencer.";
  } finally {
    isLoading.value = false;
  }
};

const fieldError = (field) => errors.value[field]?.[0] || "";
</script>

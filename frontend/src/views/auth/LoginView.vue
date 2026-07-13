<template>
  <form class="w-full max-w-sm" @submit.prevent="handleSubmit">
    <p class="mb-2 text-xs font-bold uppercase tracking-[0.25em] text-rose-300">Administrator access</p>
    <h1 class="text-3xl font-bold text-white">Welcome back</h1>
    <p class="mt-2 text-sm text-slate-300">Sign in to manage influencers and moderate reviews.</p>
    <div class="pt-7">
        <div class="mb-4">
          <label class="mb-2 block text-sm font-bold text-rose-100" for="email">Email</label>
          <input id="email" class="w-full rounded-xl border border-white/20 bg-white px-4 py-3 text-gray-900" v-model.trim="email" type="email" autocomplete="email" placeholder="arthur.white@example.net" />
          <p class="mt-1 text-xs text-red-300">{{ email_error }}</p>
        </div>
        <div class="mb-6">
          <label class="mb-2 block text-sm font-bold text-rose-100" for="password">Password</label>
          <input id="password" class="w-full rounded-xl border border-white/20 bg-white px-4 py-3 text-gray-900" v-model="password" type="password" autocomplete="current-password" placeholder="Enter your password" />
          <p class="mt-1 text-xs text-red-300">{{ password_error }}</p>
        </div>
        <p v-if="general_error" class="mb-4 rounded-lg bg-red-950/50 p-3 text-sm text-red-200" role="alert">{{ general_error }}</p>
        <div>
          <button class="ir-button-primary w-full px-5 py-3 disabled:opacity-60" type="submit" :disabled="authStore.isLoading">{{ authStore.isLoading ? "Signing in..." : "Sign in" }}</button>
        </div>
        <p class="mt-5 text-xs text-slate-400">Local seed: arthur.white@example.net / Test_123</p>
    </div>
  </form>
</template>


<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRoute, useRouter } from "vue-router";

const email = ref("");
const email_error = ref("");
const password = ref("");
const password_error = ref("");
const general_error = ref("");

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const validationPassed = () => {
  let is_valid = true;

  if (email.value === "") {
    email_error.value = "Email is required";
    is_valid = false;
  } else {
    email_error.value = "";
  }

  if (password.value === "") {
    password_error.value = "Password is required";
    is_valid = false;
  } else {
    password_error.value = "";
  }

  return is_valid;
};

const handleClearFields = () => {
  email.value = "";
  password.value = "";
  email_error.value = "";
  password_error.value = "";
};

const handleSubmit = async () => {
  general_error.value = "";

  if (!validationPassed()) {
    return;
  }

  const result = await authStore.login({
    email: email.value,
    password: password.value,
  });

  if (!result.success) {
    email_error.value = result.errors.email?.[0] || "";
    password_error.value = result.errors.password?.[0] || "";
    general_error.value = result.message;
    return;
  }

  handleClearFields();

  const redirect = typeof route.query.redirect === "string" ? route.query.redirect : null;
  await router.push(redirect || { name: "dashboard" });
};
</script>

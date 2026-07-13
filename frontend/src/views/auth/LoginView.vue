<template>
  <div class="ir-loader" v-if="authStore.isLoading"></div>
  <form class="w-full flex items-center justify-center flex-col" @submit.prevent="handleSubmit">
    <h1 class="text-teal-300 text-2xl font-semibold ir-text">Rate Influencers</h1>
    <div class="w-full max-w-xs">
      <div class="rounded pt-6 pb-8 mb-4">
        <div class="mb-4">
          <label class="block text-teal-500 text-sm font-bold mb-2" for="email"> Email </label>
          <input id="email" class="shadow appearance-none border rounded w-full py-2 px-3 leading-tight mb-3 focus:outline-none focus:shadow-outline" v-model="email" type="email" autocomplete="email" placeholder="Email" />
          <p class="text-red-500 text-xs italic">{{ email_error }}</p>
        </div>
        <div class="mb-6">
          <label class="block text-teal-500 text-sm font-bold mb-2" for="password"> Password </label>
          <input id="password" class="shadow appearance-none border rounded w-full py-2 px-3 mb-3 leading-tight focus:outline-none focus:shadow-outline" v-model="password" type="password" autocomplete="current-password" placeholder="************" />
          <p class="text-red-500 text-xs italic">{{ password_error }}</p>
        </div>
        <p v-if="general_error" class="mb-4 text-red-500 text-sm" role="alert">{{ general_error }}</p>
        <div class="flex items-center justify-between">
          <button class="bg-teal-500 hover:bg-teal-900 disabled:opacity-60 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" :disabled="authStore.isLoading">Sign In</button>
          <a class="inline-block align-baseline font-bold text-sm text-teal-500 hover:text-teal-200" href="#"> Forgot Password? </a>
        </div>
      </div>
    </div>
  </form>
</template>


<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRoute, useRouter } from "vue-router";

const email = ref("arthur.white@example.net");
const email_error = ref("");
const password = ref("Test_123");
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

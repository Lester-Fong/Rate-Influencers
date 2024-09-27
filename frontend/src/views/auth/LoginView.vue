<template>
  <div class="loading" v-if="isLoading"></div>
  <form class="w-full flex items-center justify-center flex-col">
    <h1 class="text-teal-300 text-2xl font-semibold ir-text">Rate Influencers</h1>
    <div class="w-full max-w-xs">
      <form class="rounded pt-6 pb-8 mb-4">
        <div class="mb-4">
          <label class="block text-teal-500 text-sm font-bold mb-2" for="email"> Email </label>
          <input class="shadow appearance-none border rounded w-full py-2 px-3 text-teal-500 leading-tight mb-3 focus:outline-none focus:shadow-outline" v-model="email" type="email" placeholder="Email" />
          <p class="text-red-500 text-xs italic">{{ email_error }}</p>
        </div>
        <div class="mb-6">
          <label class="block text-teal-500 text-sm font-bold mb-2" for="password"> Password </label>
          <input class="shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-teal-500 mb-3 leading-tight focus:outline-none focus:shadow-outline" v-model="password" type="password" placeholder="************" />
          <p class="text-red-500 text-xs italic">{{ password_error }}</p>
        </div>
        <div class="flex items-center justify-between">
          <button class="bg-teal-500 hover:bg-teal-900 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button" @click="onSubmit">Sign In</button>
          <a class="inline-block align-baseline font-bold text-sm text-teal-500 hover:text-teal-200" href="#"> Forgot Password? </a>
        </div>
      </form>
    </div>
  </form>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";

const isLoading = ref(false);
const email = ref("");
const email_error = ref("");
const password = ref("");
const password_error = ref("");

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

const onSubmit = async () => {
  // if (validationPassed()) {
  const response = await authStore.login({
    email: email.value,
    password: password.value,
  });
  console.log(response);
  if (response.error) {
    email_error.value = response.email[0];
    password_error.value = response.password[0];
  } else {
    handleClearFields();
    console.log("authStore: ", authStore.admin_record);
  }
  // }
};
</script>

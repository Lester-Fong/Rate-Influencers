import axios from "axios";
import { defineStore } from "pinia";
import { checkTokenAndSetAxios } from "@/utils/helper";
checkTokenAndSetAxios();
axios.defaults.baseURL = "/api"; // This ensures Axios respects the proxy

export const useAuthStore = defineStore("authStore", {
  state: () => ({
    admin_record: null,
  }),
  actions: {
    async getCSRFToken() {
      let response = await axios.get("/sanctum/csrf-cookie");
      if (response.status === 204) {
        return true;
      }
      return false;
    },
    async login(data) {
      try {
        const response = await axios.post("/api/login", data);
        this.admin_record = response.data.admin;
        return response.data;
      } catch (error) {
        Object.assign(error.response.data.errors, { error: true });
        return error.response.data.errors;
      }
    },
    async logout() {
      await axios.post("/api/logout");
      this.admin_record = null;
    },
  },
});

import axios from "axios";
import { defineStore } from "pinia";
axios.defaults.baseURL = "/"; // This ensures Axios respects the proxy

export const useAuthStore = defineStore("authStore", {
  state: () => ({
    admin_record: null,
  }),
  actions: {
    async login(data) {
      try {
        const response = await axios.post("/api/login", data);
        this.admin_record = response.data;
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

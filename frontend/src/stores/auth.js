import axios from "axios";
import { defineStore } from "pinia";
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = "/api";

export const useAuthStore = defineStore("authStore", {
  state: () => ({
    admin_record: null,
  }),
  actions: {
    async getCSRFToken() {
      try {
        const response = await axios.get("/sanctum/csrf-cookie", {
          baseURL: "",
        });
        return response.status === 204;
      } catch (error) {
        console.error("CSRF Token initialization failed:", error);
        return false;
      }
    },
    async login(data) {
      try {
        const response = await axios.post("/login", data);
        this.admin_record = response.data.admin;
        return response.data;
      } catch (error) {
        const errors = error.response?.data?.errors || { general: ["Server error"] };
        Object.assign(errors, { error: true });
        return errors;
      }
    },
    async logout() {
      try {
        await axios.post("/logout");
      } catch (error) {
        console.error("Logout failed:", error);
      } finally {
        this.admin_record = null;
      }
    },
  },
});

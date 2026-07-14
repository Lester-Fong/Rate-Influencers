import { defineStore } from "pinia";
import api, { sanctumCsrfUrl } from "@/lib/http";

export const useAuthStore = defineStore("authStore", {
  state: () => ({
    admin: null,
    initialized: false,
    isLoading: false,
    errorMessage: "",
  }),
  getters: {
    isAuthenticated: (state) => Boolean(state.admin),
  },
  actions: {
    async getCSRFToken() {
      const response = await api.get(sanctumCsrfUrl, {
        baseURL: "",
      });

      return response.status === 204;
    },
    async fetchCurrentAdmin() {
      this.isLoading = true;
      this.errorMessage = "";

      try {
        const response = await api.get("/v1/auth/me");
        this.admin = response.data.data;
        return true;
      } catch (error) {
        this.admin = null;

        if (error.response?.status !== 401) {
          this.errorMessage = "Unable to verify the current session.";
        }

        return false;
      } finally {
        this.initialized = true;
        this.isLoading = false;
      }
    },
    async login(data) {
      this.isLoading = true;
      this.errorMessage = "";

      try {
        await this.getCSRFToken();

        const response = await api.post("/v1/auth/login", data);
        this.admin = response.data.data;
        this.initialized = true;

        return { success: true, admin: this.admin };
      } catch (error) {
        this.admin = null;
        this.initialized = true;
        this.errorMessage = error.response?.data?.message || "Unable to sign in.";

        return {
          success: false,
          errors: error.response?.data?.errors || {},
          message: this.errorMessage,
        };
      } finally {
        this.isLoading = false;
      }
    },
    async logout() {
      this.isLoading = true;
      this.errorMessage = "";

      try {
        await api.post("/v1/auth/logout");
        this.admin = null;
        this.initialized = true;
        return true;
      } catch (error) {
        if (error.response?.status === 401) {
          this.admin = null;
          this.initialized = true;
          return true;
        }

        this.errorMessage = "Unable to sign out. Please try again.";
        return false;
      } finally {
        this.isLoading = false;
      }
    },
  },
});

import axios from "axios";
import { defineStore } from "pinia";
axios.defaults.baseURL = "/api"; // This ensures Axios respects the proxy

export const useInfluencerStore = defineStore("influencerStore", {
  actions: {
    async getInfluencers() {
      try {
        const response = await axios.get("/");
        if (typeof response.data === "object") {
          return response.data.data;
        } else {
          return { error: true };
        }
      } catch (error) {
        return { error: true };
      }
    },

    async showInfluencer(slug) {
      try {
        const response = await axios.get(`/${slug}`);
        return response.data;
      } catch (error) {
        return { error: true };
      }
    },

    async addInfluencerComment(data) {
      const response = await axios.post(`/${data.slug}`, data);
      return response.data;
    },
  },
});

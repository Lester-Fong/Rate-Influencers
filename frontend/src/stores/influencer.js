import axios from "axios";
import { defineStore } from "pinia";
axios.defaults.baseURL = "/"; // This ensures Axios respects the proxy
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

export const useInfluencerStore = defineStore("influencerStore", {
  actions: {
    async getInfluencers() {
      const response = await axios.get("/api/");
      return response.data;
    },

    async showInfluencer(slug) {
      const response = await axios.get(`/api/${slug}`);
      return response.data;
    },

    async addInfluencerComment(data) {
      const response = await axios.post(`/api/${data.slug}`, data);
      return response.data;
    },
  },
});

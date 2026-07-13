import axios from "axios";
import { defineStore } from "pinia";
axios.defaults.baseURL = "/api"; // This ensures Axios respects the proxy

export const useInfluencerStore = defineStore("influencerStore", {
  actions: {
    async getInfluencers(search = "") {
      const response = await axios.get("/v1/influencers", {
        params: search ? { search } : {},
      });

      return response.data.data;
    },

    async showInfluencer(slug) {
      const response = await axios.get(`/v1/influencers/${slug}`);
      return response.data.data;
    },

    async getAdminInfluencers() {
      const response = await axios.get("/v1/admin/influencers");
      return response.data.data;
    },
    async createInfluencer(data) {
      const response = await axios.post("/v1/admin/influencers", data);
      return response.data.data;
    },
    async updateInfluencer(id, data) {
      const response = await axios.patch(`/v1/admin/influencers/${id}`, data);
      return response.data.data;
    },
    async deleteInfluencer(id) {
      await axios.delete(`/v1/admin/influencers/${id}`);
    },
  },
});

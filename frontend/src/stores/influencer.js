import { defineStore } from "pinia";
import api from "@/lib/http";

export const useInfluencerStore = defineStore("influencerStore", {
  actions: {
    async getInfluencers(search = "") {
      const response = await api.get("/v1/influencers", {
        params: search ? { search } : {},
      });

      return response.data.data;
    },

    async showInfluencer(slug) {
      const response = await api.get(`/v1/influencers/${slug}`);
      return response.data.data;
    },

    async getAdminInfluencers() {
      const response = await api.get("/v1/admin/influencers");
      return response.data.data;
    },
    async createInfluencer(data) {
      const response = await api.post("/v1/admin/influencers", data);
      return response.data.data;
    },
    async updateInfluencer(id, data) {
      const response = await api.patch(`/v1/admin/influencers/${id}`, data);
      return response.data.data;
    },
    async deleteInfluencer(id) {
      await api.delete(`/v1/admin/influencers/${id}`);
    },
  },
});

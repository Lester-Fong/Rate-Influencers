import axios from "axios";
import { defineStore } from "pinia";

export const useReviewStore = defineStore("reviewStore", {
  actions: {
    async submitReview(slug, data) {
      const response = await axios.post(`/v1/influencers/${slug}/reviews`, data);
      return response.data;
    },

    async getAdminReviews() {
      const response = await axios.get("/v1/admin/reviews");
      return response.data.data;
    },

    async approveReview(id) {
      const response = await axios.post(`/v1/admin/reviews/${id}/approve`);
      return response.data.data;
    },

    async rejectReview(id) {
      const response = await axios.post(`/v1/admin/reviews/${id}/reject`);
      return response.data.data;
    },
  },
});

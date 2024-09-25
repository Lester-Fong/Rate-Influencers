import axios from "axios";
import { ref, computed } from "vue";
import { defineStore } from "pinia";

export const useAxiosRequest = defineStore("axios_request", () => {
  const actions = {
    getInfluencers: async () => {
      const response = await axios.get("/api/influencers");
      return response.data;
    },
  };

  return { actions };
});

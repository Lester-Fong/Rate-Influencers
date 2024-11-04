<template>
  <div>
    <h1 class="mb-5 ir-text font-bold text-gray-500">INFLUENCERS</h1>
    <table-lite :is-loading="table.isLoading" :columns="table.columns" :rows="table.rows" :total="table.totalRecordCount" :sortable="table.sortable" :messages="table.messages" @do-search="doSearch" @is-finished="table.isLoading = false" />
  </div>
</template>

<script setup>
import axios from "axios";
import { reactive, onMounted, ref } from "vue";
import TableLite from "vue3-table-lite";
import { useInfluencerStore } from "../../stores/influencer";

const influencerStore = useInfluencerStore();
const table = reactive({
  isLoading: false,
  columns: [
    {
      label: "Rating",
      field: "rating",
      sortable: true,
      width: "3%",
    },
    {
      label: "Name",
      field: "name",
      sortable: true,
      width: "10%",
    },
    {
      label: "Slug",
      field: "slug",
      sortable: true,
      width: "10%",
    },
    {
      label: "Created",
      field: "created_at",
      sortable: true,
      width: "10%",
    },
  ],
  rows: [],
  totalRecordCount: 0,
  sortable: {
    order: "id",
    sort: "asc",
  },
});
onMounted(() => {
  onPopulateInfluencers();
});

const onPopulateInfluencers = async () => {
  table.isLoading = true;
  const response = await influencerStore.getInfluencers();

  if (response.error) {
    error_message.value = "No influencers found";
    console.log("Error: ", response.error);
    return;
  }

  table.rows = await response;
  table.isLoading = false;
  table.totalRecordCount = await response.length;
};

const doSearch = (offset, limit, order, sort) => {
  table.isLoading = true;

  // Start use axios to get data from Server
  let url = "https://www.example.com/api/some_endpoint?offset=" + offset + "&limit=" + limit + "&order=" + order + "&sort=" + sort;
  axios.get(url).then((response) => {
    // refresh table rows
    table.rows.value = response.rows;
    table.totalRecordCount.value = response.count;
    table.sortable.value.order.value = order;
    table.sortable.value.sort.value = sort;
  });
};
</script>

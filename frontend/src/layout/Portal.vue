<template>
  <div class="flex items-center justify-between">
    <aside class="w-80 ir-bg-primary">
      <div class="flex items-center justify-between">
        <Sidebar />
      </div>
    </aside>
    <main class="bg-gray-100">
      <div class="pt-20 px-7">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>


<script setup>
import Sidebar from "@/components/Sidebar.vue";
</script>
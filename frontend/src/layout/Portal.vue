<template>
  <div class="flex items-center justify-between">
    <aside class="w-80 h-screen bg-sky-900">
      <div class="flex items-center justify-between">
        <Sidebar />
      </div>
    </aside>
    <main class="bg-gray-100">
      <router-view></router-view>
    </main>
  </div>
</template>


<script setup>
import Sidebar from "@/components/Sidebar.vue";
</script>
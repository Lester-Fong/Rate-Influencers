<template>
  <div class="admin-shell flex" :data-theme="theme">
    <button v-if="sidebarOpen" type="button" class="fixed inset-0 z-40 bg-black/55 md:hidden" aria-label="Close navigation" @click="sidebarOpen = false"></button>

    <aside class="admin-sidebar admin-sidebar-drawer shrink-0 md:sticky md:top-0 md:block md:h-screen md:w-64 md:translate-x-0" :class="{ 'is-open': sidebarOpen }">
      <Sidebar @navigate="sidebarOpen = false" />
    </aside>

    <div class="min-w-0 flex-1">
      <header class="admin-topbar sticky top-0 z-30 flex items-center justify-between gap-4 border-x-0 border-t-0 px-4 py-3 sm:px-6">
        <div class="flex items-center gap-3">
          <button type="button" class="admin-theme-button p-2 md:hidden" aria-label="Open navigation" @click="sidebarOpen = true">
            <span aria-hidden="true">&#9776;</span>
          </button>
          <div>
            <p class="text-sm font-bold">Administrator portal</p>
            <p class="admin-muted hidden text-xs sm:block">{{ authStore.admin?.fullname || authStore.admin?.email }}</p>
          </div>
        </div>

        <button type="button" class="admin-theme-button px-3 py-2 text-sm font-semibold" :aria-pressed="theme === 'dark'" @click="toggleTheme">
          {{ theme === "dark" ? "Light mode" : "Dark mode" }}
        </button>
      </header>

      <main class="mx-auto min-h-[calc(100vh-4rem)] max-w-7xl p-4 sm:p-6 lg:p-8">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import Sidebar from "@/components/Sidebar.vue";
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
const sidebarOpen = ref(false);
const storedTheme = localStorage.getItem("admin-theme");
const theme = ref(storedTheme || (window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"));

const toggleTheme = () => {
  theme.value = theme.value === "dark" ? "light" : "dark";
  localStorage.setItem("admin-theme", theme.value);
};
</script>

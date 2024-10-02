<template>
  <div class="flex items-center justify-between">
    <aside class="w-3/12 h-screen bg-rose-900">
      <div class="flex items-center justify-between p-4">
        <div class="flex items-center">
          <img src="https://via.placeholder.com/150" alt="logo" class="w-10 h-10 rounded-full" />
          <h1 class="text-white text-lg ml-2">Admin</h1>
        </div>
        <button class="text-white focus:outline-none">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
      </div>
    </aside>
    <main class="w-9/12 bg-gray-100">
      <router-view></router-view>
    </main>
  </div>
</template>
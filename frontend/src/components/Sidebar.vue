<template>
  <div class="flex items-start justify-between flex-col w-full">
    <div class="mt-5 mb-10 text-center w-full font-bold text-2xl">
      <h1 class="text-teal-300 ir-main_title text-2xl tracking-wider">Rate Influencers</h1>
      <hr class="mt-4 opacity-25 mx-4" />
    </div>
    <ul class="px-4 w-full">
      <li class="pb-3 cursor-pointer text-gray-400 font-medium hover:text-sky-400">
        <router-link :to="{ name: 'dashboard' }">Dashboard</router-link>
      </li>
      <li class="pb-3 cursor-pointer text-gray-400 font-medium hover:text-sky-400">
        <router-link :to="{ name: 'dashboard' }">Influencers</router-link>
      </li>
      <li class="pb-3 cursor-pointer text-gray-400 font-medium hover:text-sky-400">
        <router-link :to="{ name: 'dashboard' }">Dashboard</router-link>
      </li>
    </ul>
  </div>
</template>
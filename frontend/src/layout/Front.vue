<template>
  <div class="ir-main">
    <header class="ir-site-header">
      <div class="mx-auto flex max-w-7xl flex-col items-center gap-5">
        <router-link to="/" class="ir-main_title uppercase ir-text-main" aria-label="RateInfluencers home">
          Influencer <span class="ir-text-secondary">Reviews</span>
        </router-link>
        <nav class="flex items-center gap-5 text-base font-semibold" aria-label="Primary navigation">
          <router-link :to="{ name: 'home' }" class="text-rose-200 hover:text-white">Discover</router-link>
          <router-link :to="{ name: 'about' }" class="text-rose-200 hover:text-white">About</router-link>
          <router-link :to="{ name: 'login' }" class="rounded-full border border-rose-300/50 px-4 py-2 text-rose-100 hover:bg-white/10">Admin</router-link>
        </nav>
      </div>
    </header>

    <router-view></router-view>
  </div>
</template>

<template>
  <main class="auth-shell grid md:grid-cols-2">
    <div class="auth-art flex items-end p-6 sm:p-10">
      <router-link :to="{ name: 'home' }" class="text-sm font-bold text-white/90 hover:text-white">&larr; Back to public site</router-link>
    </div>
    <div class="auth-panel flex items-center justify-center p-6 sm:p-10">
      <router-view></router-view>
    </div>
  </main>
</template>

<template>
  <main class="ir-page">
    <section class="ir-container mx-auto max-w-4xl p-7 sm:p-10">
      <p class="mb-2 text-sm font-bold uppercase tracking-[0.22em] text-rose-300">Our purpose</p>
      <h1 class="ir-text-main text-3xl font-bold sm:text-5xl">About RateInfluencers</h1>
      <div class="mt-6 space-y-5 text-base leading-relaxed ir-text-secondary sm:text-lg">
        <p>
          RateInfluencers helps people discover digital creators through community reviews of their public content.
        </p>
        <p>
          Visitors can browse influencer profiles, read reviews approved by the site administrator, and submit a one-to-five-star review for moderation.
        </p>
        <p>
          Ratings and review counts are based only on approved reviews, keeping pending or rejected submissions out of public results.
        </p>
      </div>
      <router-link :to="{ name: 'home' }" class="ir-button-primary mt-8 inline-block px-5 py-3">
        Browse influencers
      </router-link>
    </section>
  </main>
</template>

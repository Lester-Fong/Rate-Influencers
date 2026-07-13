<template>
  <section class="mx-auto w-11/12 pb-16 lg:w-8/12">
    <div class="ir-container p-8 md:p-12">
      <h1 class="ir-text-main ir-text-main_title">About RateInfluencers</h1>
      <div class="mt-6 space-y-5 ir-text ir-text-secondary">
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
      <router-link :to="{ name: 'home' }" class="mt-8 inline-block rounded-lg bg-emerald-800 px-5 py-3 font-semibold text-white hover:bg-emerald-700">
        Browse influencers
      </router-link>
    </div>
  </section>
</template>

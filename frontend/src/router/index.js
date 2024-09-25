import { createRouter, createWebHistory } from "vue-router";
const HomeView = () => import("../views/HomeView.vue");
const aboutView = () => import("../views/AboutView.vue");
const InfluencerView = () => import("../views/InfluencerView.vue");
const LoginView = () => import("../views/LoginView.vue");

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/login",
      name: "Login",
      component: LoginView,
      meta: {
        requiresAuth: false,
        layout: "auth",
      },
    },
    {
      path: "/",
      meta: {
        requiresAuth: false,
        layout: "front",
      },
      children: [
        {
          path: "",
          name: "home",
          component: HomeView,
        },
        {
          path: "/:influencerSlug",
          name: "influencer",
          component: InfluencerView,
        },
        {
          path: "/about",
          name: "about",
          component: aboutView,
        },
      ],
    },
  ],
});

export default router;

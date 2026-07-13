import { createRouter, createWebHistory } from "vue-router";
import { pinia } from "../stores";
import { useAuthStore } from "../stores/auth";
const HomeView = () => import("../views/HomeView.vue");
const aboutView = () => import("../views/AboutView.vue");
const InfluencerView = () => import("../views/InfluencerView.vue");
const LoginView = () => import("../views/auth/LoginView.vue");

const DashboardView = () => import("../views/portal/DashboardView.vue");
const InfluencerPortalView = () => import("../views/portal/InfluencerView.vue");
const ReviewsPortalView = () => import("../views/portal/ReviewsView.vue");

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // ===================  LOGIN ROUTES  =================== >
    {
      path: "/login",
      meta: {
        requiresAuth: false,
        layout: "auth",
      },
      children: [
        {
          path: "",
          name: "login",
          component: LoginView,
        },
      ],
    },
    //  ===================  PORTAL ROUTES  =================== >
    {
      path: "/admin",
      meta: {
        requiresAuth: true,
        layout: "portal",
      },
      children: [
        {
          path: "dashboard",
          name: "dashboard",
          component: DashboardView,
        },
        {
          path: "influencer",
          name: "influencerPortal",
          component: InfluencerPortalView,
        },
        {
          path: "reviews",
          name: "reviewsPortal",
          component: ReviewsPortalView,
        },
      ],
    },
    // ===================  PUBLIC ROUTES  =================== >
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

router.beforeEach(async (to) => {
  const authStore = useAuthStore(pinia);

  if (!authStore.initialized) {
    await authStore.fetchCurrentAdmin();
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: "login", query: { redirect: to.fullPath } };
  }

  if (to.name === "login" && authStore.isAuthenticated) {
    return { name: "dashboard" };
  }

  return true;
});

export default router;

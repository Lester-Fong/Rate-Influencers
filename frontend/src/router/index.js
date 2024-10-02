import { createRouter, createWebHistory } from "vue-router";
const HomeView = () => import("../views/HomeView.vue");
const aboutView = () => import("../views/AboutView.vue");
const InfluencerView = () => import("../views/InfluencerView.vue");
const LoginView = () => import("../views/auth/LoginView.vue");

const DashboardView = () => import("../views/portal/DashboardView.vue");

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

router.beforeEach((to, from, next) => {
  const token = sessionStorage.getItem("api-token");
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    // this route requires auth, check if logged in
    // if not, redirect to login page.
    if (!token) {
      next({
        name: "login",
      });
    }
  } else {
    if (token) {
      sessionStorage.removeItem("api-token");
    }
  }
  next(); // make sure to always call next()!
});

export default router;

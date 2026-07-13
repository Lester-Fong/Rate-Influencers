import { createApp } from "vue";
import { pinia } from "./stores";

import "./assets/main.css";
import { OhVueIcon, addIcons } from "oh-vue-icons";
import { BiFacebook, BiInstagram, BiYoutube, BiTiktok, OiPlus } from "oh-vue-icons/icons";
addIcons(BiFacebook, BiInstagram, BiYoutube, BiTiktok, OiPlus);

import App from "./App.vue";
import router from "./router";
import StarRating from "vue-star-rating";

const app = createApp(App);

app.use(pinia);
app.use(router);
app.component("star-rating", StarRating);
app.component("v-icon", OhVueIcon);

app.mount("#app");

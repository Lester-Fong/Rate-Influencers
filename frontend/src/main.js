import { createApp } from "vue";
import { createPinia } from "pinia";

import "./assets/main.css";
import { OhVueIcon, addIcons } from "oh-vue-icons";
import { BiFacebook, BiInstagram, BiYoutube, BiTiktok, BiHeart, BiHeartFill, OiPlus } from "oh-vue-icons/icons";
addIcons(BiFacebook, BiInstagram, BiYoutube, BiTiktok, BiHeart, BiHeartFill, OiPlus);

import App from "./App.vue";
import router from "./router";
import StarRating from "vue-star-rating";

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.component("star-rating", StarRating);
app.component("v-icon", OhVueIcon);
// app.config.globalProperties.$swal = Swal;

app.mount("#app");

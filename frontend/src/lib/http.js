import axios from "axios";

const apiOrigin = String(import.meta.env.VITE_API_URL || "")
  .trim()
  .replace(/\/+$/, "");

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = `${apiOrigin}/api`;

export const sanctumCsrfUrl = `${apiOrigin}/sanctum/csrf-cookie`;
export default axios;

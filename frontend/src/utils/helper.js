// src/globalFunctions.js

import axios from "axios";

// Function to get a specific cookie by name
const getCookie = (name) => {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(";").shift();
  return null;
};

// Function to check if a token exists and set axios withCredentials
const checkTokenAndSetAxios = () => {
  const hasToken = getCookie("XSRF-TOKEN") ? true : false;
  axios.defaults.withCredentials = hasToken;
};

// Export all global functions
export { getCookie, checkTokenAndSetAxios };

import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import axios from 'axios';




const app = createApp(App)
const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL,
});


app.use(router)

app.mount('#app')
export default api;
import axios from 'axios';
import { createPinia } from 'pinia';
import { createApp } from 'vue';
import InventoryApp from './vue/InventoryApp.vue';
import router from './vue/router';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

if (csrfToken) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

const app = createApp(InventoryApp);
app.use(createPinia());
app.use(router);
app.mount('#inventory-app');

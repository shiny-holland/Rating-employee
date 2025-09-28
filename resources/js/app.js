import { createApp } from 'vue';
import router from './routes';

// Create the app
const app = createApp({});

// Use router
app.use(router);

// Mount the app
app.mount('#app');
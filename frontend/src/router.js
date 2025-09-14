import { createMemoryHistory, createRouter } from 'vue-router';

import Checkout from './pages/Checkout.vue';
import Families from './pages/Families.vue';
import Checkin from './pages/Checkin.vue';

const routes = [
  { path: '/', redirect: { path: '/checkout' } },
  { path: '/checkout', component: Checkout },
  { path: '/families', component: Families },
  { path: '/checkin', component: Checkin },
];

export const router = createRouter({
  history: createMemoryHistory(),
  routes,
});
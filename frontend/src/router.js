import { createMemoryHistory, createRouter } from 'vue-router';

import Checkout from './components/Checkout.vue';
import Families from './components/Families.vue';
import Checkin from './components/Checkin.vue';

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
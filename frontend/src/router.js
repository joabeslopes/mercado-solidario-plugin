import { createMemoryHistory, createRouter } from 'vue-router';

import Checkout from './components/Checkout.vue';
import Family from './components/Family.vue';

const routes = [
  { path: '/', redirect: { path: '/checkout' } },
  { path: '/checkout', component: Checkout },
  { path: '/families', component: Family },
];

export const router = createRouter({
  history: createMemoryHistory(),
  routes,
});
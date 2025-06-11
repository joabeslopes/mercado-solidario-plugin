import { createMemoryHistory, createRouter } from 'vue-router';

import Checkout from './components/Checkout.vue';
import Families from './components/Families.vue';

const routes = [
  { path: '/', redirect: { path: '/checkout' } },
  { path: '/checkout', component: Checkout },
  { path: '/families', component: Families },
];

export const router = createRouter({
  history: createMemoryHistory(),
  routes,
});
import { createMemoryHistory, createRouter } from 'vue-router';

import Stock from './components/Stock.vue';
import Family from './components/Family.vue';

const routes = [
  { path: '/', component: Stock },
  { path: '/familys', component: Family },
];

export const router = createRouter({
  history: createMemoryHistory(),
  routes,
});
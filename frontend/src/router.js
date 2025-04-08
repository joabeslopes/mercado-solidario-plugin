import { createMemoryHistory, createRouter } from 'vue-router';

import Stock from './components/Stock.vue';
import People from './components/People.vue';

const routes = [
  { path: '/', component: Stock },
  { path: '/people', component: People },
];

export const router = createRouter({
  history: createMemoryHistory(),
  routes,
});
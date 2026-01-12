import { createMemoryHistory, createRouter } from 'vue-router';

import FamilyCRUD from '../../components/FamilyCRUD.vue';
import FamilyQueue from '../../components/FamilyQueue.vue';

const routes = [
  { path: '/', redirect: { path: '/crud' } },
  { path: '/crud', component: FamilyCRUD },
  { path: '/queue', component: FamilyQueue }
];

export const router = createRouter({
  history: createMemoryHistory(),
  routes,
});
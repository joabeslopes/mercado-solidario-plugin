import { createMemoryHistory, createRouter } from 'vue-router';
import Cadastro from './Cadastro.vue';
import Search from './Search.vue';
import Dependant from './Dependant.vue';
import Relation from './Relation.vue';

const routes = [
  { path: '/', redirect: { path: '/crud' } },
  { path: '/crud', component: Cadastro },
  { path: '/search', component: Search },
  { path: '/dependant', component: Dependant },
  { path: '/relation', component: Relation }
];

export const router = createRouter({
  history: createMemoryHistory(),
  routes,
});
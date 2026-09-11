import { createMemoryHistory, createRouter } from 'vue-router';
import ReportView from './ReportView.vue';

const routes = [
  { path: '/', redirect: { path: '/checkin' } },
  { path: '/checkin', component: ReportView, props: { type: 'checkin' } },
  { path: '/checkout', component: ReportView, props: { type: 'checkout' } }
];

export const router = createRouter({
  history: createMemoryHistory(),
  routes,
});

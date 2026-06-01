import { createRouter, createWebHistory } from 'vue-router'
import ProxyListPage from '@/pages/ProxyListPage.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'proxies',
      component: ProxyListPage,
    },
  ],
})

export default router

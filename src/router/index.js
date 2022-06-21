import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'HomePage',
    component: () => import( '../views/HomePage.vue'),
    meta: {
      title: 'Player'
    }
  },
  {
    path: '/test',
    name: 'TestPage',
    component: () => import( '../views/TestPage.vue'),
    meta: {
      title: 'Test'
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  linkActiveClass: 'active',
  linkExactActiveClass: 'exact-active'
})

router.beforeEach((to) => {
  document.title = to.meta.title
})

export default router

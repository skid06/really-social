import { useUserStore } from 'src/stores/user'

const routes = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/login',
    component: () => import('pages/Login.vue'),
    beforeEnter: (to, from, next) => {
      const userStore = useUserStore()
      userStore.loadUserFromLocalStorage()
      if (userStore.user) {
        next('/blogs')
      } else {
        next()
      }
    },
  },

  {
    path: '/registration',
    component: () => import('pages/Registration.vue'),
    beforeEnter: (to, from, next) => {
      const userStore = useUserStore()
      userStore.loadUserFromLocalStorage()
      if (userStore.user) {
        next('/blogs')
      } else {
        next()
      }
    },
  },
  {
    path: '/blogs',
    component: () => import('layouts/MainLayout.vue'),
    children: [{ path: '', component: () => import('pages/BlogManagement.vue') }],
    meta: { requiresAuth: true },
  },

  // 404 route
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
]

export default routes

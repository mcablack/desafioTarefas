import Vue from 'vue';
import VueRouter from 'vue-router';

// Layouts
import BaseLayout from '@/layouts/BaseLayout.vue';

// Views
import Login from '@/views/Login.vue';
import Home from '@/views/Home.vue';
import TaskList from '@/views/TaskList.vue';

Vue.use(VueRouter);

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false }
  },
  {
    path: '/',
    component: BaseLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/tarefas'
      },
      {
        path: 'home',
        name: 'Home',
        component: Home
      },
      {
        path: 'tarefas',
        name: 'Tarefas',
        component: TaskList
      }
    ]
  },
  {
    path: '*',
    redirect: '/login'
  }
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
});

// Guard de autenticação
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);

  if (requiresAuth && !token) {
    next('/login');
  } else if (to.path === '/login' && token) {
    next('/tarefas');
  } else {
    next();
  }
});

export default router;
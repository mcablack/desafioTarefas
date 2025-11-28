import Vue from 'vue';
import App from './App.vue';
import axios from './axios';
import store from './store';
import router from './router';

Vue.config.productionTip = false;
Vue.prototype.$http = axios;

// Flag global para evitar inicialização duplicada (HMR)
let appInitialized = false;

    // ================================
    // INICIALIZAÇÃO OTIMIZADA
    // ================================
  async function init() {
    if (appInitialized) {
      console.log('[main.js] App já inicializado, pulando init()');
      return;
    }

    appInitialized = true;

    const token = localStorage.getItem('token');
    const currentPath = router.currentRoute.path; 

    // Rotas públicas que NÃO precisam validar token
    const publicRoutes = ['/login', '/register', '/forgot-password'];
    const isPublicRoute = publicRoutes.includes(currentPath);

    if (!token || isPublicRoute) {
      store.commit('CLEAR_AUTH'); 
      return;
    }

    // Busca o usuário logado usando o token
    try {
      await store.dispatch('fetchUser');
    } catch (error) {
      console.warn('Falha ao buscar usuário na inicialização:', error);
      store.commit('CLEAR_AUTH');
    }
  }


  init().finally(() => {
    new Vue({
      router,
      store,
      render: h => h(App)
    }).$mount('#app');
  });

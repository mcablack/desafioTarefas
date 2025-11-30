import Vue from 'vue';
import App from './App.vue';
import axios from './axios';
import store from './store';
import router from './router';
import Vuelidate from 'vuelidate'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(Vuelidate)

Vue.config.productionTip = false;
Vue.prototype.$http = axios;


let appInitialized = false;

async function init() {
  if (appInitialized) {
    console.log('[main.js] App já inicializado, pulando init()');
    return;
  }

  appInitialized = true;

  const token = localStorage.getItem('token');
  const currentPath = router.currentRoute.path;

  const publicRoutes = ['/login', '/register', '/forgot-password'];
  const isPublicRoute = publicRoutes.includes(currentPath);

  if (!token || isPublicRoute) {
    store.commit('CLEAR_AUTH');
    return;
  }

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

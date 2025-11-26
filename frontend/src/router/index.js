import Vue from 'vue';
import Router from 'vue-router';
import Home from '../views/Home.vue';
import Mensagem from '../views/Mensagem.vue';

Vue.use(Router);

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    {
      path: '/mensagem',
      name: 'Mensagem',
      component: Mensagem
    }
  ]
});
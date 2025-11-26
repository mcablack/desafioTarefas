import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import axios from 'axios';


axios.defaults.baseURL = process.env.VUE_APP_API_URL || 'http://localhost:8080/api';


new Vue({
router,
store,
render: h => h(App)
}).$mount('#app');
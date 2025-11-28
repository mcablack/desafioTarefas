import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: null,
    token: localStorage.getItem('token') || null
  },

  mutations: {
    SET_USER(state, user) {
      state.user = user;
    },

    SET_TOKEN(state, token) {
      state.token = token;
      if (token) {
        localStorage.setItem('token', token);
      } else {
        localStorage.removeItem('token');
      }
    },

    CLEAR_AUTH(state) {
      state.user = null;
      state.token = null;
      localStorage.removeItem('token');
    }
  },

  actions: {
    login({ commit }, { token, user }) {
      commit('SET_TOKEN', token);
      commit('SET_USER', user);
    },

    logout({ commit }) {
      commit('CLEAR_AUTH');
    },

    async fetchUser({ commit, state }) {
      if (!state.token) return;

      try {
        const { data } = await axios.get('/auth/me', {
          headers: { Authorization: `Bearer ${state.token}` }
        });
        commit('SET_USER', data);
      } catch (error) {
        console.error('Falha ao carregar usuário:', error);
        commit('CLEAR_AUTH');
      }
    }
  },

  getters: {
    isAuthenticated: state => !!state.token,
    currentUser: state => state.user,
    empresaId: state => state.user?.empresa_id
  }
});

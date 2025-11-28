/**
 * Configuração global do Axios para a aplicação
 * 
 * Ações:
 * 1. Definir URL base da API (http://localhost:8000/api)
 * 2. Intercepta requisições para adicionar token JWT automaticamente no header Authorization
 * 3. Intercepta respostas para tratar erros 401 (não autorizado):
 *    - Remove token inválido do localStorage
 *    - Redireciona usuário para tela de login
 */

import axios from 'axios';
import router from './router'; 


axios.defaults.baseURL = process.env.VUE_APP_API_URL || 'http://localhost:8000/api';

// Request interceptor: adiciona token se existir
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, error => Promise.reject(error));

// Response interceptor: trata 401 globalmente (desloga e manda ao login)
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('token');
      // evitar loop caso já estejamos em /login
      if (router.currentRoute.path !== '/login') {
        router.push('/login');
      }
    }
    return Promise.reject(error);
  }
);

export default axios;

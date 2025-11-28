<template>
  <div class="login-container">
    <div class="login-card">
      <img
        src="https://cdn-icons-png.flaticon.com/512/5087/5087579.png"
        class="login-logo"
        alt="Logo"
      />
      <h3 class="login-title">Acessar Sistema</h3>
      
      <div v-if="error" class="alert-error">
        {{ error }}
      </div>
      
      <form @submit.prevent="login">
        <div class="form-group">
          <label>Email</label>
          <input 
            type="email" 
            v-model="email" 
            class="form-input" 
            placeholder="seu@email.com"
            required 
          />
        </div>
        
        <div class="form-group">
          <label>Senha</label>
          <input 
            type="password" 
            v-model="password" 
            class="form-input"
            placeholder="••••••••" 
            required 
          />
        </div>
        
        <button class="btn-login" :disabled="loading">
          <span v-if="loading">Validando...</span>
          <span v-else>Entrar</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "LoginPage",
  
  data() {
    return {
      email: "",
      password: "",
      loading: false,
      error: null,
    };
  },
  
  methods: {
    async login() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await this.$http.post("/auth/login", {
          email: this.email,
          password: this.password,
        });
        
        localStorage.setItem("token", response.data.access_token);
        
        this.$store.commit('SET_TOKEN', response.data.access_token);

        // Salva usuário no Vuex
        this.$store.commit("SET_USER", response.data.user);
        
        
        this.$router.push("/home");
      } catch (err) {
        this.error = "Usuário ou senha incorretos.";
      }
      
      this.loading = false;
    },
  },
};
</script>

<script>
export default {
  name: "LoginPage",
  
  data() {
    return {
      email: "",
      password: "",
      loading: false,
      error: null,
    };
  },
  
  methods: {
    async login() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await this.$http.post("/auth/login", {
          email: this.email,
          password: this.password,
        });

        // SALVA TOKEN NO LOCALSTORAGE
        localStorage.setItem("token", response.data.access_token);

        // SALVA NO VUEX
        this.$store.commit("SET_USER", response.data.user);

        // Redireciona
        this.$router.push("/tarefas");
      } catch (err) {
        this.error = "Usuário ou senha incorretos.";
      }
      
      this.loading = false;
    },
  },
};
</script>





<style scoped>
.login-container {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
  position: relative;
  overflow: hidden;
}

.login-container::before {
  content: '';
  position: absolute;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
  border-radius: 50%;
  top: -200px;
  right: -200px;
  animation: float 8s ease-in-out infinite;
}

.login-container::after {
  content: '';
  position: absolute;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(168, 85, 247, 0.08) 0%, transparent 70%);
  border-radius: 50%;
  bottom: -150px;
  left: -150px;
  animation: float 10s ease-in-out infinite reverse;
}

@keyframes float {
  0%, 100% { transform: translate(0, 0); }
  50% { transform: translate(30px, 30px); }
}

.login-card {
  width: 420px;
  background: rgba(20, 20, 20, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  padding: 48px 40px;
  box-shadow: 
    0 20px 60px rgba(0, 0, 0, 0.5),
    0 0 0 1px rgba(255, 255, 255, 0.05);
  text-align: center;
  position: relative;
  z-index: 1;
  transition: transform 0.3s ease;
}

.login-card:hover {
  transform: translateY(-5px);
}

.login-logo {
  width: 90px;
  margin-bottom: 24px;
  filter: drop-shadow(0 4px 12px rgba(59, 130, 246, 0.3));
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

.login-title {
  color: #ffffff;
  font-size: 26px;
  font-weight: 600;
  margin-bottom: 32px;
  letter-spacing: -0.5px;
}

.alert-error {
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.3);
  color: #fca5a5;
  padding: 12px;
  border-radius: 10px;
  margin-bottom: 24px;
  font-size: 14px;
  animation: slideDown 0.3s ease;
}

@keyframes slideDown {
  from { 
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-group {
  margin-bottom: 24px;
  text-align: left;
}

.form-group label {
  display: block;
  color: #a1a1aa;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 8px;
  letter-spacing: 0.3px;
}

.form-input {
  width: 100%;
  padding: 14px 16px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  color: #ffffff;
  font-size: 15px;
  transition: all 0.3s ease;
  outline: none;
}

.form-input::placeholder {
  color: rgba(255, 255, 255, 0.3);
}

.form-input:focus {
  background: rgba(255, 255, 255, 0.08);
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.btn-login {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  border: none;
  border-radius: 12px;
  color: white;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 8px;
  position: relative;
  overflow: hidden;
}

.btn-login::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s ease;
}

.btn-login:hover::before {
  left: 100%;
}

.btn-login:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
  box-shadow: 0 8px 24px rgba(59, 130, 246, 0.4);
  transform: translateY(-2px);
}

.btn-login:active:not(:disabled) {
  transform: translateY(0);
}

.btn-login:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
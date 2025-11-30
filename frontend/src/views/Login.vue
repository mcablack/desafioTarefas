<template>
  <div class="login-page">
    <b-container>
      <b-row align-h="center" align-v="center" class="min-vh-100">
        <b-col cols="12" md="6" lg="5" xl="4">
          <b-card class="shadow-lg border-0">
            <div class="text-center mb-4">
              <b-icon 
                icon="clipboard-check" 
                font-scale="3" 
                variant="primary"
              ></b-icon>
              <h3 class="mt-3 mb-1">Sistema de Tarefas</h3>
              <p class="text-muted">Faça login para continuar</p>
            </div>

            <!-- ALERT DE ERRO -->
            <b-alert v-if="error" variant="danger" show dismissible @dismissed="error = null">
              <b-icon icon="exclamation-circle"></b-icon>
              {{ error }}
            </b-alert>

            <!-- FORMULÁRIO -->
            <b-form @submit.prevent="login">
              <!-- EMAIL -->
              <b-form-group
                label="Email"
                label-for="email"
              >
                <b-input-group>
                  <b-input-group-prepend is-text>
                    <b-icon icon="envelope"></b-icon>
                  </b-input-group-prepend>
                  <b-form-input
                    id="email"
                    v-model="email"
                    type="email"
                    placeholder="seu@email.com"
                    required
                    trim
                  ></b-form-input>
                </b-input-group>
              </b-form-group>

              <!-- SENHA -->
              <b-form-group
                label="Senha"
                label-for="password"
              >
                <b-input-group>
                  <b-input-group-prepend is-text>
                    <b-icon icon="lock"></b-icon>
                  </b-input-group-prepend>
                  <b-form-input
                    id="password"
                    v-model="password"
                    type="password"
                    placeholder="••••••••"
                    required
                  ></b-form-input>
                </b-input-group>
              </b-form-group>

              <!-- BOTÃO LOGIN -->
              <b-button
                type="submit"
                variant="primary"
                block
                size="lg"
                :disabled="loading"
                class="mt-3"
              >
                <b-spinner v-if="loading" small class="mr-2"></b-spinner>
                <span v-if="loading">Validando...</span>
                <span v-else>
                  <b-icon icon="box-arrow-in-right"></b-icon>
                  Entrar
                </span>
              </b-button>
            </b-form>

            <!-- FOOTER DO CARD -->
            <div class="text-center mt-4">
              <small class="text-muted">
                Use as credenciais fornecidas para acessar
              </small>
            </div>
          </b-card>

          <!-- INFO EXTRA -->
          <div class="text-center mt-3">
            <small class="text-muted">
              <b-icon icon="info-circle"></b-icon>
              Primeiro acesso? Use: admin@techsolutions.com / password
            </small>
          </div>
        </b-col>
      </b-row>
    </b-container>
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

          // SALVA TOKEN NO LOCALSTORAGE
          localStorage.setItem("token", response.data.access_token);

          // SALVA NO VUEX
          this.$store.commit("SET_TOKEN", response.data.access_token);
          this.$store.commit("SET_USER", response.data.user);

          this.$bvToast.toast(`Bem-vindo, ${response.data.user.name}!`, {
            title: 'Login realizado',
            variant: 'success',
            solid: true,
            autoHideDelay: 3000
          });

          // Redireciona
          this.$router.push("/tarefas");
        } catch (err) {
          this.error = "Email ou senha incorretos. Verifique suas credenciais.";
          
          this.$bvToast.toast('Falha na autenticação', {
            title: 'Erro',
            variant: 'danger',
            solid: true
          });
        }
        
        this.loading = false;
      },
    },
  };
</script>

<style scoped>
  .login-page {
    background: linear-gradient(135deg, #101214ff 0%, #0871e9ff 100%);
    min-height: 100vh;
  }

  .card {
    border-radius: 1rem;
  }
</style>
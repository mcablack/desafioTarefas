<template>
  <div class="layout">

    <!-- HEADER -->
    <header class="topbar">
      <div class="topbar-center">
        <strong class="app-name">Sistema de Tarefas</strong>
        <div class="user-info">
          <span class="user-name">{{ user?.name }}</span>
          <span class="empresa-name">- {{ user?.empresa.nome}}</span>
        </div>
      </div>

      <div class="topbar-right">
        <button class="logout-btn" @click="logout">Sair</button>
      </div>
    </header>

    <!-- CONTEÚDO -->
    <main class="content">
      <router-view />
    </main>

  </div>
</template>

<script>
export default {
  name: "BaseLayout",

  computed: {
    user() {
      return this.$store.state.user;
    }
  },

  methods: {
    async logout() {
      try {
        await this.$http.post("/auth/logout");
      } catch (e) {
        console.warn("Falha ao deslogar, mas removendo sessão local.");
      }

      localStorage.removeItem("token");
      this.$store.commit("SET_USER", null);

      this.$router.push("/login");
    }
  }
};
</script>

<style scoped>
/* Layout geral */
.layout {
  display: flex;
  flex-direction: column;
  height: 100vh;
  font-family: 'Segoe UI', Roboto, sans-serif;
  background: #f4f6f9;
}

/* =====================
   Topbar Profissional
===================== */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 32px;
  background: linear-gradient(135deg, #1a73e8, #1557b0);
  color: #fff;
  position: sticky;
  top: 16px; /* Distância do topo para arredondar visualmente */
  margin: 0 16px;
  border-radius: 12px;
  z-index: 1000;
  box-shadow: 0 8px 16px rgba(0,0,0,0.15);
  transition: all 0.3s ease;
}

/* Centralizado */
.topbar-center {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  text-align: center;
}

.app-name {
  font-size: 20px;
  font-weight: 700;
  margin-bottom: 6px;
  text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.user-info {
  font-size: 14px;
  font-weight: 500;
  color: #dbeafe;
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}

/* Botão à direita */
.topbar-right {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.logout-btn {
  background: #fff;
  color: #1a73e8;
  border: none;
  padding: 8px 18px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

.logout-btn:hover {
  background: #f0f0f0;
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(0,0,0,0.25);
}

/* Conteúdo principal */
.content {
  flex: 1;
  padding: 24px;
  overflow-y: auto;
}

/* =====================
   Responsividade
===================== */
@media (max-width: 768px) {
  .topbar {
    flex-direction: column;
    gap: 12px;
    padding: 16px;
    margin: 16px;
  }

  .topbar-center {
    align-items: center;
  }

  .topbar-right {
    width: 100%;
    justify-content: center;
  }
}
</style>

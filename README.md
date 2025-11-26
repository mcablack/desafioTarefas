# 🚀 Sistema de Gerenciamento de Tarefas

Sistema multi-empresa para gerenciamento de tarefas com Laravel (Backend) e Vue.js (Frontend).

---

## 📋 Requisitos

- Docker Desktop instalado e rodando
- Git instalado

---

## 🔧 Como Rodar o Projeto

### 1️⃣ Clonar o repositório

```bash
git clone https://github.com/mcablack/desafioTarefas.git
cd desafioTarefas
```

### 2️⃣ Subir os containers

```bash
docker-compose up -d
```

### 3️⃣ Configurar o Backend (Laravel)

```bash
# Entrar no container do backend
docker exec -it desafio_backend bash

# Executar os comandos abaixo dentro do container:
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
exit
```

### 4️⃣ Configurar o Frontend (Vue.js)

```bash
# Entrar no container do frontend
docker exec -it desafio_frontend bash

# Executar os comandos abaixo dentro do container:
npm install
npm run dev

# Para sair: Ctrl+C, depois digite exit
```

---

## ✅ Pronto! Acesse:

- **Backend API:** http://localhost:8000
- **Frontend:** http://localhost:8080   ou 5173

---

## 👥 Usuários para Login

Use qualquer um destes usuários de teste:

| Empresa | Email | Senha |
|---------|-------|-------|
| Tech Solutions | admin@techsolutions.com | password |
| Tech Solutions | maria@techsolutions.com | password |
| Digital Marketing | admin@digitalmarketing.com | password |
| Digital Marketing | ana@digitalmarketing.com | password |
| StartupX | admin@startupx.com | password |

---

## 🛠️ Comandos Úteis

### Parar o projeto
```bash
docker-compose down
```

### Ver logs
```bash
docker-compose logs -f
```

### Resetar banco de dados
```bash
docker exec -it desafio_backend bash
php artisan migrate:fresh --seed
exit
```

### Entrar nos containers
```bash
# Backend
docker exec -it desafio_backend bash

# Frontend
docker exec -it desafio_frontend bash
```

---

## 🐛 Problemas Comuns

**Porta já está em uso?**
- Verifique se já tem algo rodando nas portas 8000, 5173 ou 3306
- Pare outros serviços ou altere as portas no `docker-compose.yml`

**Erro de permissão?**
```bash
docker exec -it desafio_backend bash
chmod -R 775 storage bootstrap/cache
exit
```

**Container não inicia?**
```bash
docker-compose down
docker-compose up -d --force-recreate
```

---

## 📦 Estrutura do Projeto

```
desafioTarefas/
├── backend/          # API Laravel
├── frontend/         # Interface Vue.js
└── docker-compose.yml
```
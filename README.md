# 🚀 Sistema de Gerenciamento de Tarefas

Sistema multi-empresa para gerenciamento de tarefas com Laravel e Docker.

## 📋 Requisitos

- Docker Desktop instalado
- Git instalado

## 🔧 Como Rodar o Projeto

### 1. Clonar o repositório
```bash
git clone https://github.com/mcablack/desafioTarefas.git
cd desafioTarefas
```

### 2. Copiar arquivo de ambiente
```bash
cp backend/.env.example backend/.env
```

### 3. Subir os containers Docker
```bash
docker-compose up -d
```

### 4. Instalar dependências do Composer
```bash
docker-compose exec app composer install
```

### 5. Gerar chave da aplicação
```bash
docker-compose exec app php artisan key:generate
```

### 6. Rodar as migrations
```bash
docker-compose exec app php artisan migrate
```

### 7. Rodar os seeders
```bash
docker-compose exec app php artisan db:seed
```

## 🌐 Acessar a Aplicação

Abra o navegador: **http://localhost:8000**

## 👥 Usuários de Teste

### Empresa 1: Tech Solutions
- Email: `admin@techsolutions.com`
- Senha: `password`

### Empresa 2: Digital Marketing
- Email: `admin@digitalmarketing.com`
- Senha: `password`

### Empresa 3: StartupX
- Email: `admin@startupx.com`
- Senha: `password`

## 🛠️ Comandos Úteis
```bash
# Parar containers
docker-compose down

# Ver logs
docker-compose logs -f

# Resetar banco de dados
docker-compose exec app php artisan migrate:fresh --seed

# Acessar container
docker-compose exec app bash
```

## 📦 Estrutura
```
├── backend/          # Laravel API
├── frontend/         # Vue.js (em desenvolvimento)
├── docker/           # Configurações Docker
└── docker-compose.yml
```
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

2️⃣ Subir os containers

    docker-compose up --build -d

3️⃣ Configurar o Backend (Laravel)
    # Entrar no container do backend
    docker exec -it desafio_backend bash

    # Executar os comandos abaixo dentro do container:
    composer install
    cp .env.example .env
    php artisan key:generate         # Gerar chave da aplicação
    php artisan jwt:secret           # Gerar secret JWT
    php artisan migrate              # Criar tabelas, incluindo jobs
    php artisan db:seed              # Popular banco com dados iniciais

    # Rodar o worker da fila para processar jobs (email, notificações, etc.)
    php artisan queue:work


4️⃣ Configurar o Frontend (Vue.js)
    $Obs: Importante
        Abri outro console, caso queira usar o mesmo console, terá que sair (exit) e depois entrar no backend pra rodar o php artisan queue:work novamente. 

    # Entrar no container do frontend
    docker exec -it desafio_frontend bash

    # Executar os comandos abaixo dentro do container:
    npm install

    # Para sair: Ctrl+C, depois digite exit

    ✅ Pronto! Acesse:

    Frontend: http://localhost:8080 


    Backend API: http://localhost:8000

    MailHog: http://localhost:8025  //Lembre-se de rodar php artisan queue:work  

    👥 Usuários para Login
    Use qualquer um destes usuários de teste:
| Empresa           | Email                                                           | Senha    |
| ----------------- | --------------------------------------------------------------- | -------- |
| Tech Solutions    | [admin@techsolutions.com](mailto:admin@techsolutions.com)       | password |
| Tech Solutions    | [maria@techsolutions.com](mailto:maria@techsolutions.com)       | password |
| Digital Marketing | [admin@digitalmarketing.com](mailto:admin@digitalmarketing.com) | password |
| Digital Marketing | [ana@digitalmarketing.com](mailto:ana@digitalmarketing.com)     | password |
| StartupX          | [admin@startupx.com](mailto:admin@startupx.com)                 | password |

   🛠️ Comandos Úteis
    
    docker exec -it desafio_backend bash
    php artisan migrate:fresh --seed
    exit

    Rodar fila de jobs manualmente, faz com que dispare o email.
        php artisan queue:work

    Problemas Comuns

    Porta já está em uso?

    Verifique se já tem algo rodando nas portas 8000, 5173 ou 3306

    Pare outros serviços ou altere as portas no docker-compose.yml

    Erro de permissão?
        docker exec -it desafio_backend bash
        chmod -R 775 storage bootstrap/cache
        exit
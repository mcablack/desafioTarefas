<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Tarefa;
use Carbon\Carbon;

class InitialSeeder extends Seeder
{
    public function run()
    {
        // ========================================
        // EMPRESA 1: Tech Solutions
        // ========================================
        $empresa1 = Empresa::firstOrCreate(
            ['nome' => 'Tech Solutions'],
            ['identificador' => 'tech-solutions']
        );

        $admin1 = User::firstOrCreate(
            ['email' => 'admin@techsolutions.com'],
            [
                'name' => 'João Silva',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa1->id
            ]
        );

        User::firstOrCreate(
            ['email' => 'maria@techsolutions.com'],
            [
                'name' => 'Maria Santos',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa1->id
            ]
        );

        // Tarefas da Empresa 1
        $this->criarTarefas($empresa1->id, [
            [
                'titulo' => 'Implementar autenticação JWT',
                'descricao' => 'Configurar sistema de autenticação com tokens JWT no backend Laravel',
                'status' => 'em_andamento',
                'prioridade' => 'alta',
                'data_limite' => Carbon::now()->addDays(3)
            ],
            [
                'titulo' => 'Revisar código do módulo de relatórios',
                'descricao' => 'Fazer code review e sugerir melhorias',
                'status' => 'pendente',
                'prioridade' => 'media',
                'data_limite' => Carbon::now()->addDays(7)
            ],
            [
                'titulo' => 'Deploy em produção',
                'descricao' => 'Realizar deploy da versão 2.0 no servidor de produção',
                'status' => 'concluida',
                'prioridade' => 'alta',
                'data_limite' => Carbon::now()->subDays(2)
            ],
            [
                'titulo' => 'Documentar API REST',
                'descricao' => 'Criar documentação completa dos endpoints da API',
                'status' => 'pendente',
                'prioridade' => 'baixa',
                'data_limite' => Carbon::now()->addDays(15)
            ],
            [
                'titulo' => 'Corrigir bug no login mobile',
                'descricao' => 'Usuários relatam erro 500 ao fazer login pelo app',
                'status' => 'pendente',
                'prioridade' => 'alta',
                'data_limite' => Carbon::now()->addDay() 
            ],
            [
                'titulo' => 'Atualizar dependências do projeto',
                'descricao' => 'Atualizar Laravel para versão 11 e outras libs',
                'status' => 'pendente',
                'prioridade' => 'baixa',
                'data_limite' => null // Sem prazo
            ]
        ]);

        // ========================================
        // EMPRESA 2: Digital Marketing
        // ========================================
        $empresa2 = Empresa::firstOrCreate(
            ['nome' => 'Digital Marketing'],
            ['identificador' => 'digital-marketing']
        );

        $admin2 = User::firstOrCreate(
            ['email' => 'admin@digitalmarketing.com'],
            [
                'name' => 'Pedro Costa',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa2->id
            ]
        );

        User::firstOrCreate(
            ['email' => 'ana@digitalmarketing.com'],
            [
                'name' => 'Ana Paula',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa2->id
            ]
        );

        $this->criarTarefas($empresa2->id, [
            [
                'titulo' => 'Criar campanha Black Friday',
                'descricao' => 'Planejar e executar campanha de marketing para Black Friday',
                'status' => 'em_andamento',
                'prioridade' => 'alta',
                'data_limite' => Carbon::now()->addDays(5)
            ],
            [
                'titulo' => 'Análise de métricas mensais',
                'descricao' => 'Gerar relatório de performance do mês anterior',
                'status' => 'pendente',
                'prioridade' => 'media',
                'data_limite' => Carbon::now()->addDays(10)
            ],
            [
                'titulo' => 'Reunião com cliente novo',
                'descricao' => 'Apresentação de proposta para cliente XYZ Corp',
                'status' => 'concluida',
                'prioridade' => 'alta',
                'data_limite' => Carbon::now()->subDays(1)
            ],
            [
                'titulo' => 'Criar posts para redes sociais - Dezembro',
                'descricao' => 'Preparar calendário editorial completo',
                'status' => 'pendente',
                'prioridade' => 'media',
                'data_limite' => Carbon::now()->addDays(20)
            ]
        ]);

        // ========================================
        // EMPRESA 3: StartupX
        // ========================================
        $empresa3 = Empresa::firstOrCreate(
            ['nome' => 'StartupX'],
            ['identificador' => 'startupx']
        );

        $admin3 = User::firstOrCreate(
            ['email' => 'admin@startupx.com'],
            [
                'name' => 'Carlos Oliveira',
                'password' => Hash::make('password'),
                'empresa_id' => $empresa3->id
            ]
        );

        $this->criarTarefas($empresa3->id, [
            [
                'titulo' => 'Pitch para investidores',
                'descricao' => 'Preparar apresentação para rodada de investimento Seed',
                'status' => 'em_andamento',
                'prioridade' => 'alta',
                'data_limite' => Carbon::now()->addDays(2)
            ],
            [
                'titulo' => 'Contratar desenvolvedor full-stack',
                'descricao' => 'Processo seletivo para vaga de desenvolvedor',
                'status' => 'pendente',
                'prioridade' => 'alta',
                'data_limite' => Carbon::now()->addDays(14)
            ],
            [
                'titulo' => 'Validar MVP com usuários beta',
                'descricao' => 'Coletar feedback de 50 usuários teste',
                'status' => 'pendente',
                'prioridade' => 'media',
                'data_limite' => null
            ]
        ]);        
      
    }

    private function criarTarefas($empresaId, array $tarefas)
    {
        foreach ($tarefas as $tarefa) {
            Tarefa::firstOrCreate(
                [
                    'empresa_id' => $empresaId,
                    'titulo' => $tarefa['titulo']
                ],
                [
                    'descricao' => $tarefa['descricao'],
                    'status' => $tarefa['status'],
                    'prioridade' => $tarefa['prioridade'],
                    'data_limite' => $tarefa['data_limite']
                ]
            );
        }
    }
}
<?php

namespace Database\Factories;

use App\Models\Tarefa;
use Illuminate\Database\Eloquent\Factories\Factory;

class TarefaFactory extends Factory
{
    protected $model = Tarefa::class;

    public function definition()
    {
        return [
            'empresa_id' => 1,
            'titulo' => $this->faker->sentence(rand(3, 6)),
            'descricao' => $this->faker->optional(0.7)->paragraph(),
            'status' => $this->faker->randomElement(['pendente', 'em_andamento', 'concluida']),
            'prioridade' => $this->faker->randomElement(['baixa', 'media', 'alta']),
            'data_limite' => $this->faker->optional(0.8)->dateTimeBetween('now', '+30 days')
        ];
    }

    // States para criar tarefas específicas

    public function urgente()
    {
        return $this->state(function (array $attributes) {
            return [
                'prioridade' => 'alta',
                'data_limite' => now()->addDays(rand(1, 3)),
                'status' => 'pendente'
            ];
        });
    }

    public function atrasada()
    {
        return $this->state(function (array $attributes) {
            return [
                'data_limite' => now()->subDays(rand(1, 7)),
                'status' => $this->faker->randomElement(['pendente', 'em_andamento'])
            ];
        });
    }

    public function concluida()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'concluida'
            ];
        });
    }
}
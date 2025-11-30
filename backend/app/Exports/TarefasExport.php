<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TarefasExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $tarefas;

    public function __construct($tarefas)
    {
        $this->tarefas = $tarefas;
    }

    /**
     * Retorna a coleção de dados
     */
    public function collection()
    {
        return $this->tarefas;
    }

    /**
     * Define os cabeçalhos das colunas
     */
    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Descrição',
            'Status',
            'Prioridade',
            'Data Limite',
            'Criado em',
            'Atualizado em'
        ];
    }

    /**
     * Mapeia os dados para cada linha
     */
    public function map($tarefa): array
    {
        return [
            $tarefa->id,
            $tarefa->titulo,
            $tarefa->descricao ?? 'Sem descrição',
            $this->formatStatus($tarefa->status),
            $this->formatPrioridade($tarefa->prioridade),
            $tarefa->data_limite ? $tarefa->data_limite->format('d/m/Y') : 'Sem prazo',
            $tarefa->created_at->format('d/m/Y H:i'),
            $tarefa->updated_at->format('d/m/Y H:i')
        ];
    }

    /**
     * Estiliza a planilha
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Estilo do cabeçalho
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1a73e8']
                ],
                'alignment' => ['horizontal' => 'center']
            ],
        ];
    }

    /**
     * Formata status para português
     */
    private function formatStatus($status)
    {
        $map = [
            'pendente' => 'Pendente',
            'em_andamento' => 'Em Andamento',
            'concluida' => 'Concluída'
        ];
        return $map[$status] ?? $status;
    }

    /**
     * Formata prioridade para português
     */
    private function formatPrioridade($prioridade)
    {
        $map = [
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta' => 'Alta'
        ];
        return $map[$prioridade] ?? $prioridade;
    }
}
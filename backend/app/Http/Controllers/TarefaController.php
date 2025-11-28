<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use App\Jobs\SendTaskEmailJob;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'tenant']);
    }

    // ========================================
    // Lista tarefas com filtros
    // ========================================
    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        $query = Tarefa::where('empresa_id', $empresaId);

        // Filtros
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->prioridade) {
            $query->where('prioridade', $request->prioridade);
        }

        return $query->orderBy('id', 'desc')->paginate(10);
    }

    // ========================================
    // Cria nova tarefa + ENVIA EMAIL
    // ========================================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|in:pendente,em_andamento,concluida',
            'prioridade' => 'required|in:baixa,media,alta',
            'data_limite' => 'nullable|date'
        ]);

        $validated['empresa_id'] = auth()->user()->empresa_id;
        $validated['user_id'] = auth()->id();

        $tarefa = Tarefa::create($validated);

        // DISPARA EMAIL NA FILA (assíncrono)
        SendTaskEmailJob::dispatch($tarefa, 'criada', auth()->user()->email);

        return response()->json([
            'message' => 'Tarefa criada com sucesso! Email será enviado em breve.',
            'tarefa' => $tarefa
        ], 201);
    }

    // ========================================
    // Exibe uma tarefa
    // ========================================
    public function show($id)
    {
        $tarefa = Tarefa::where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($id);

        return $tarefa;
    }

    // ========================================
    // Atualiza tarefa + ENVIA EMAIL SE CONCLUÍDA
    // ========================================
    public function update(Request $request, $id)
    {
        $tarefa = Tarefa::where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($id);

        $statusAnterior = $tarefa->status;

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|in:pendente,em_andamento,concluida',
            'prioridade' => 'required|in:baixa,media,alta',
            'data_limite' => 'nullable|date'
        ]);

        $tarefa->update($validated);

        //  SE MUDOU PARA CONCLUÍDA, ENVIA EMAIL 
        if ($statusAnterior !== 'concluida' && $tarefa->status === 'concluida') {
            SendTaskEmailJob::dispatch($tarefa, 'concluida', auth()->user()->email);
        }

        return response()->json([
            'message' => 'Tarefa atualizada com sucesso!',
            'tarefa' => $tarefa
        ]);
    }

    // ========================================
    // Deleta tarefa
    // ========================================
    public function destroy($id)
    {
        $tarefa = Tarefa::where('empresa_id', auth()->user()->empresa_id)
            ->findOrFail($id);

        $tarefa->delete();

        return response()->json(['message' => 'Tarefa removida com sucesso']);
    }
}    

    
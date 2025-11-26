<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth:api','tenant']);
    }
    // Lista tarefas com filtros
    public function index(Request $request)
    {
        $empresaId = auth()->user()->empresa_id;

        $query = Tarefa::where('empresa_id', $empresaId);

        // filtros
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->prioridade) {
            $query->where('prioridade', $request->prioridade);
        }

        return $query->orderBy('id', 'desc')->paginate(10);
    }

    // Cria nova tarefa
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|in:pendente,em andamento,concluida',
            'prioridade' => 'required|in:baixa,media,alta',
            'data_limite' => 'nullable|date'
        ]);

        $validated['empresa_id'] = auth()->user()->empresa_id;
        $validated['user_id'] = auth()->id();

        return Tarefa::create($validated);
    }

    // Exibe uma tarefa
    public function show($id)
    {
        $tarefa = Tarefa::where('empresa_id', auth()->user()->empresa_id)
                        ->findOrFail($id);

        return $tarefa;
    }

    // Atualiza tarefa
    public function update(Request $request, $id)
    {
       $tarefa = Tarefa::where('empresa_id', auth()->user()->empresa_id)
                        ->findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|in:pendente,em andamento,concluida',
            'prioridade' => 'required|in:baixa,media,alta',
            'data_limite' => 'nullable|date'
        ]);

        $tarefa->update($validated);

        return $tarefa;
    }

    // Deleta tarefa
    public function destroy($id)
    {
        $tarefa = Tarefa::where('empresa_id', auth()->user()->empresa_id)
                        ->findOrFail($id);

        $tarefa->delete();

        return response()->json(['message' => 'Tarefa removida com sucesso']);
    }
}    

    
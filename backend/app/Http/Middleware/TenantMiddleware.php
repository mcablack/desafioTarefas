<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Tarefa;

class TenantMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        /**
         * Middleware Tenant
         * ---------------------------------------------------------------
         * O propósito deste middleware é garantir que **todo acesso**
         * às tarefas seja automaticamente filtrado pelo `empresa_id`
         * do usuário autenticado.
         *
         * Isso impede que um usuário visualize, edite ou exclua tarefas
         * de outra empresa.
         *
         * Adicionei o  Global Scope que é nativo do eloquent que faz com que qualquer  consulta
         * ao model Tarefa (index, update, show...) já venha filtrada
         * por empresa_id, reforçando a segurança multi-tenant.
         * ---------------------------------------------------------------
         */

        \App\Models\Tarefa::addGlobalScope('empresa', function ($query) use ($user) {
            $query->where('empresa_id', $user->empresa_id);
        });

        return $next($request);
    }
}

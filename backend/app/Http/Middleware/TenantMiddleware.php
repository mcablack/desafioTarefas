<?php
namespace App\Http\Middleware;
use Closure;

class TenantMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Força que as queries filtrem por empresa_id
        \App\Models\Tarefa::addGlobalScope('empresa', function ($query) use ($user) {
            $query->where('empresa_id', $user->empresa_id);
        });

        return $next($request);
    }
}

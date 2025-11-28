<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TarefaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =======================
// Rotas Públicas
// =======================
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/register-user', [AuthController::class, 'registerUser']);

// =======================
// Rotas Protegidas (JWT + Tenant Middleware)
// =======================
Route::middleware(['auth:api', 'tenant'])->group(function () {

    // Autenticação
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // CRUD de Tarefas
    Route::apiResource('tarefas', TarefaController::class);

    // EXPORTAÇÃO DE TAREFAS
    Route::get('tarefas-export/excel', [TarefaController::class, 'export'])->name('tarefas.export');
    Route::get('tarefas-export/csv', [TarefaController::class, 'exportCsv'])->name('tarefas.export.csv');
});

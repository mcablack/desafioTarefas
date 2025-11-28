<?php

namespace App\Jobs;

use App\Models\Tarefa;
use App\Models\User;
use App\Mail\TarefaCriada;
use App\Mail\TarefaConcluida;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTaskEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tarefa;
    protected $tipoEmail;
    protected $userEmail;

    /**
     * Número de tentativas em caso de falha
     */
    public $tries = 3;

    /**
     * Tempo de espera entre tentativas (em segundos)
     */
    public $backoff = 10;

    /**
     * Create a new job instance.
     *
     * @param Tarefa $tarefa
     * @param string $tipoEmail ('criada' ou 'concluida')
     * @param string $userEmail
     */
    public function __construct(Tarefa $tarefa, string $tipoEmail, string $userEmail)
    {
        $this->tarefa = $tarefa;
        $this->tipoEmail = $tipoEmail;
        $this->userEmail = $userEmail;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            if ($this->tipoEmail === 'criada') {
                Mail::to($this->userEmail)->send(new TarefaCriada($this->tarefa));
                Log::info("Email de tarefa criada enviado para: {$this->userEmail}");
            } elseif ($this->tipoEmail === 'concluida') {
                Mail::to($this->userEmail)->send(new TarefaConcluida($this->tarefa));
                Log::info("Email de tarefa concluída enviado para: {$this->userEmail}");
            }
        } catch (\Exception $e) {
            Log::error("Erro ao enviar email: " . $e->getMessage());
            throw $e; // Re-lança para tentar novamente
        }
    }

    /**
     * Método chamado quando o job falha após todas as tentativas
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Job falhou definitivamente: " . $exception->getMessage());
    }
}
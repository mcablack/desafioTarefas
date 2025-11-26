<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Tarefa;

class TarefaCriada extends Mailable
{
    use Queueable, SerializesModels;
    public $tarefa;
    public function __construct(Tarefa $tarefa){ $this->tarefa = $tarefa; }
    public function build()
    {
        return $this->subject('Tarefa criada')->view('emails.tarefa_criada')->with(['tarefa'=>$this->tarefa]);
    }
}

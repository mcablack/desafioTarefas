<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Tarefa;

class TarefaConcluida extends Mailable
{
    use Queueable, SerializesModels;
    public $tarefa;
    public function __construct(Tarefa $tarefa){ $this->tarefa = $tarefa; }
    public function build()
    {
        return $this->subject('Tarefa concluída')->view('emails.tarefa_concluida')->with(['tarefa'=>$this->tarefa]);
    }
}

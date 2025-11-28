<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tarefa extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'status',
        'prioridade',
        'data_limite',
        'empresa_id',
        'user_id'
    ];
    protected $casts = ['data_limite'=>'datetime'];

    
    // Relacionamento: Tarefa pertence a uma empresa
    public function empresa(){ return $this->belongsTo(Empresa::class); }
    // Relacionamento: usuário criador da tarefa
    public function user(){ return $this->belongsTo(User::class); }



}
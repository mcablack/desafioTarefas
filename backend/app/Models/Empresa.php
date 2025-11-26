<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;
    protected $fillable = ['nome','identificador'];
    public function users() { return $this->hasMany(User::class); }
    public function tarefas() { return $this->hasMany(Tarefa::class); }

}

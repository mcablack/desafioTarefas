<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    // ======================================================
    // Registrar empresa + primeiro usuário
    // ======================================================
    public function register(Request $r)
    {
        $r->validate([
            'empresa.nome' => 'required|string',
            'user.name' => 'required|string',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|min:6'
        ]);

        // Criar empresa
        $empresa = Empresa::create([
            'nome' => $r->empresa['nome'],
            'identificador' => $r->empresa['identificador']
                ?? strtolower(str_replace(' ', '-', $r->empresa['nome']))
        ]);

        // Criar usuário admin da empresa
        $user = User::create([
            'name' => $r->user['name'],
            'email' => $r->user['email'],
            'password' => Hash::make($r->user['password']),
            'empresa_id' => $empresa->id
        ]);

        // Autenticar e gerar token
        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }


    // ======================================================
    // Registrar usuário em empresa existente
    // ======================================================
    public function registerUser(Request $r)
    {
        $r->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'empresa_id' => 'required|exists:empresas,id'
        ]);

        $user = User::create([
            'name' => $r->name,
            'email' => $r->email,
            'password' => Hash::make($r->password),
            'empresa_id' => $r->empresa_id
        ]);

        $token = auth('api')->login($user);

        return $this->respondWithToken($token);
    }


    // ======================================================
    // Login
    // ======================================================
    public function login(Request $r)
    {   
        

            $credentials = $r->only('email', 'password');
        

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }


    // ======================================================
    // Usuário logado
    // ======================================================
    public function me()
    {
        return response()->json(auth('api')->user()->load('empresa')); // carrega empresa também
    }


    // ======================================================
    // Logout
    // ======================================================
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Logged out']);
    }


    // ======================================================
    // Refresh token
    // ======================================================
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }


    // ======================================================
    // Padrão de resposta JWT
    // ======================================================
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user'         => auth('api')->user()->load('empresa')
        ]);
    }
}

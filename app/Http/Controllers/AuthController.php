<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    // Login do usuário
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !\Hash::check($request->password, $user->password)) {
            return response()->json([
                'message'   => 'Credenciais inválidas.'
            ], 401);
        }

        $credentials = $request->only('email','password');

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message'   => 'Login realizado com sucesso.',
            'user' => $user,
            'token'=> $token
        ], 200);
    }

    // Logout do usuário
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    // Registro do usuário
    public function register(Request $request) {
        {
            // Validação
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ]);

            try {
                // Criação do usuário
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password) // Encriptação
                ]);

                // Geração do token
                $token = $user->createToken('API Token')->plainTextToken;

                return response()->json([
                    'message' => 'Usuário registrado com sucesso.',
                    'user' => $user,
                    'token' => $token
                ], 201);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Erro ao registrar o usuário.',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }
}

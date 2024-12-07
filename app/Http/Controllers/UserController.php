<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::all();

            return response()->json([
                'message'   => 'Usuários obtidos com sucesso!',
                'data'  => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'Erro ao obter usuários.',
                'error'     => $e->getMessage(),
            ],500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'=> 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],
                'password' => 'required|string|min:8',
            ], [
                'email.unique' => 'O e-mail informado já está em uso.',
            ]);

            $validated['password'] = bcrypt($validated['password']);

            $user = User::create($validated);

            return response()->json([
                'message'=> 'Usuário criado com sucesso',
                'data'=> $user,
            ],201);
        } catch (ValidationException $e) {
            return response()->json([
                'message'   => 'Erro de validação.',
                'errors'    => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar usuário.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'message'=> 'Usuário encontrado com sucesso!',
                'data'=> $user,
            ],200);
        } catch (\Exception $e) {
            
            return response()->json([
                'message'=> 'Erro ao obter usuário.',
                'error'=> $e->getMessage(),
            ],500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'string|max:255',
                'email' => 'string|email|max:255|unique:users,email,' . $id,
                'password' => 'nullable|string|min:8',
            ]);

            if (isset($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            }

            $user->update($validated);

            return response()->json($user);
        } catch (ValidationException $e) {
            return response()->json([
                'message'=> 'Erro de validação.',
                'error'=> $e->errors(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message'=> 'Erro ao atualizar usuário.',
                'error'=> $e->getMessage(),
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'message'   => 'Usuário excluído com sucesso!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'=> 'Erro ao excluir usuário.',
                'error'=> $e->getMessage(),
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Services\TaskService;
use App\Exceptions\TaskNotFoundException;
use App\Models\User;
use App\Models\Task;

class TaskController extends Controller
{
    private TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $userId = auth()->id();

        if ($status && !in_array($status, ['pending', 'in_progress', 'completed'], true))
            return response()->json(['error' => 'Status inválido.'], 400);

        $tasks = $this->taskService->getTasksByFilters($status, $userId);

        if ($tasks->isEmpty()) {
            $message = $status
                ? 'Nenhuma tarefa encontrada para o status especificado.'
                : 'Nenhuma tarefa encontrada.';

            return response()->json(['message' => $message], 200);
        }

        return response()->json($tasks);
    }

    public function getTaskByStatus(Request $request)
    {
        $status = $request->query('status', null);

        if (!$status && !in_array($status, ['pending', 'in_progress', 'completed'], true))
            return response()->json(['error' => 'Status inválido'], 400);
        
        $tasks = $this->taskService->getTasksByFilters($status, $userId);
        
        if ($tasks->isEmpty())
            return response()->json(['message' => 'Nenhuma tarefa encontrada para o status especificado.'], 200);

        return response()->json($tasks, 200);
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
                'title'=> 'required|string|max:255',
                'description'=> 'nullable|string',
                'status'=> 'required|in:pending,in_progress,completed',
            ]);

            $task = Task::create([
                'title'=> $validated['title'],
                'description'=> $validated['description'],
                'status'=> $validated['status'],
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Tarefa criada com sucesso!',
                'task' => $task
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar tarefa. Tente novamente',
                'error' => $e->getMessage()
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
            // Busca a tarefa com base no ID
            $task = Task::with('user')->findOrFail($id);

            return response()->json([
                'message' => 'Tarefa obtida com sucesso!',
                'data' => $task,
            ], 200);
        } catch (ModelNotFoundException $e) {
            throw new TaskNotFoundException('Tarefa não encontrada.');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao obter a tarefa.',
                'error' => $e->getMessage(),
            ], 500);
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
            $validated = $request->validate([
                'title'=> 'required|string|max:255',
                'description'=> 'nullable|string',
                'status'=> 'required|in:pending,in_progress,completed',
            ]);

            $task = Task::findOrFail($id);

            $task->update($validated);

            return response()->json([
                'message'=> 'Tarefa atualizada com sucesso.',
                'data'  => $task,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message'=> 'Tarefa não encontrada.',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação.',
                'error' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message'=> 'Erro ao atualizar a tarefa.',
                'error'=> $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, $taskId, $status): JsonResponse
    {
        if (!in_array($status, ['pending', 'in_progress', 'completed']))
            return response()->json(['error' => 'Status inválido'], 400);

        $userId = auth()->id();
        
        $updated = $this->taskService->updateTaskStatus($taskId, $status, $userId);
        
        if (!$updated) 
            return response()->json(['error' => 'Tarefa não encontrada ou não autorizada.'], 400);

        return response()->json(['message'  => 'Status atualizado com sucesso!'], 200);
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
            $task = Task::findOrFail($id);
            $task->delete();
            return response()->json([
                'message'   => 'Tarefa excluída com sucesso!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'=> 'Erro ao excluir tarefa.',
                'error'=> $e->getMessage(),
            ], 500);
        }
    }
}

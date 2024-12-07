<?php 

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Collection;

class TaskService
{
    public function getTasksByFilters(?string $status, int $userId): Collection
    {
        $query = Task::query()->where('user_id', $userId);

        if ($status) {
            $this->validateStatus($status);
            $query->where('status', $status);
        }

        return $query->with('user')->get();
    }

    private function validateStatus(string $status): void
    {
        $validStatuses = ['pending', 'in_progress', 'completed'];

        if (!in_array($status, $validStatuses, true)) {
            throw new \InvalidArgumentException('Status inválido.');
        }
    }

    public function updateTaskStatus(string $taskId, string $status, int $userId): bool
    {
        $task = Task::where('id', $taskId)
            ->where('user_id', $userId)
            ->first();

        if (!$task) {
            return false;
        }

        $task->status = $status;
        return $task->save();
    }
}
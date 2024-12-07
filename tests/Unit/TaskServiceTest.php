<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Services\TaskService;
use App\Models\Task;
use App\Models\User;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testUpdateTaskStatus()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $taskService = new TaskService();
        $status = 'completed';
        $result = $taskService->updateTaskStatus($task->id, $status, $user->id);

        $this->assertTrue($result);
        $this->assertEquals($status, $task->fresh()->status);
    }

    public function testUpdateTaskStatusNotFound()
    {
        $user = User::factory()->create();

        $taskService = new TaskService();

        $result = $taskService->updateTaskStatus(999999, 'completed', $user->id);   

        $this->assertFalse($result);
    }
}

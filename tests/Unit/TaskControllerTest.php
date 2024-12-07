<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\WithFaker;


class TaskControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testShowTask()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create(['user_id'  => $user->id]);

        $response = $this->get('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Tarefa obtida com sucesso!',
                'data' => [
                    'id' => $task->id,
                    'user_id' => $user->id
                ]
            ]);
    }

    public function testShowNotFound()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/tasks/999999');

        $response->assertStatus(404)
            ->assertJson([
                'message'=> 'Tarefa não encontrada.',
            ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    public function run()
    {
        $todos = [
            [
                'title' => 'Complete project proposal',
                'assignee' => 'John',
                'description' => 'Finish the project proposal document and send to client',
                'due_date' => now()->addDays(5),
                'status' => 'pending',
                'priority' => 'high',
            ],
            [
                'title' => 'Fix login bug',
                'assignee' => 'Doe',
                'description' => 'Investigate and fix the login authentication issue',
                'due_date' => now()->addDays(2),
                'status' => 'in_progress',
                'priority' => 'high',
            ],
            [
                'title' => 'Update documentation',
                'assignee' => 'John',
                'description' => 'Update API documentation with new endpoints',
                'due_date' => now()->addDays(10),
                'status' => 'pending',
                'priority' => 'medium',
            ],
            [
                'title' => 'Design new UI',
                'assignee' => 'Doe',
                'description' => 'Create mockups for the new user interface',
                'due_date' => now()->addDays(7),
                'status' => 'completed',
                'priority' => 'medium',
                'completed_at' => now()->subDays(2),
            ],
            [
                'title' => 'Prepare presentation',
                'assignee' => 'John',
                'description' => 'Prepare slides for quarterly review meeting',
                'due_date' => now()->addDays(3),
                'status' => 'completed',
                'priority' => 'low',
                'completed_at' => now()->subDay(),
            ],
        ];

        foreach ($todos as $todo) {
            Todo::create($todo);
        }
    }
}

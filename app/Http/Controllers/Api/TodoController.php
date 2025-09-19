<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TodosExport;

class TodoController extends Controller
{
    /**
     * Create a new todo item
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'assignee' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date|after:now',
            'status' => 'nullable|string|in:pending,in_progress,completed',
            'priority' => 'nullable|string|in:low,medium,high',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $todo = Todo::create([
            'title' => $request->title,
            'assignee' => $request->assignee,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status ?? 'pending',
            'priority' => $request->priority ?? 'medium',
        ]);

        return response()->json(['data' => $todo, 'message' => 'Todo created successfully'], 201);
    }

    /**
     * Get todos for Excel report generation
     */
    public function exportExcel(Request $request)
    {
        $query = Todo::query();

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('assignee')) {
            $query->where('assignee', $request->assignee);
        }

        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        $todos = $query->get();

        return Excel::download(new TodosExport($todos), 'todos.xlsx');
    }

    /**
     * Get chart data
     */
    public function getChartData(Request $request)
    {
        $type = $request->query('type');

        switch ($type) {
            case 'status':
                return $this->getStatusSummary();
            case 'priority':
                return $this->getPrioritySummary();
            case 'assignee':
                return $this->getAssigneeSummary();
            default:
                return response()->json(['message' => 'Invalid chart type'], 400);
        }
    }

    /**
     * Get status summary data
     */
    private function getStatusSummary()
    {
        $pendingCount = Todo::where('status', 'pending')->count();
        $inProgressCount = Todo::where('status', 'in_progress')->count();
        $completedCount = Todo::where('status', 'completed')->count();

        return response()->json([
            'status_summary' => [
                'pending' => $pendingCount,
                'in_progress' => $inProgressCount,
                'completed' => $completedCount,
            ]
        ]);
    }

    /**
     * Get priority summary data
     */
    private function getPrioritySummary()
    {
        $lowCount = Todo::where('priority', 'low')->count();
        $mediumCount = Todo::where('priority', 'medium')->count();
        $highCount = Todo::where('priority', 'high')->count();

        return response()->json([
            'priority_summary' => [
                'low' => $lowCount,
                'medium' => $mediumCount,
                'high' => $highCount,
            ]
        ]);
    }

    /**
     * Get assignee summary data
     */
    private function getAssigneeSummary()
    {
        $assignees = Todo::select('assignee')
            ->distinct()
            ->get();

        $assigneeSummary = [];

        foreach ($assignees as $assignee) {
            $name = $assignee->assignee;
            $totalTodos = Todo::where('assignee', $name)->count();
            $totalPendingTodos = Todo::where('assignee', $name)
                ->where('status', 'pending')
                ->count();
            $totalTimeTrackedCompletedTodos = Todo::where('assignee', $name)
                ->where('status', 'completed')
                ->whereNotNull('completed_at')
                ->count();

            $assigneeSummary[$name] = [
                'total_todos' => $totalTodos,
                'total_pending_todos' => $totalPendingTodos,
                'total_timetracked_completed_todos' => $totalTimeTrackedCompletedTodos,
            ];
        }

        return response()->json([
            'assignee_summary' => $assigneeSummary
        ]);
    }
}

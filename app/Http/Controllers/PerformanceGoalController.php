<?php

namespace App\Http\Controllers;

use App\Models\PerformanceGoal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceGoalController extends Controller
{
    /**
     * Display all goals
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $goals = PerformanceGoal::with('user', 'assignedBy')->paginate(15);
        } elseif ($user->isSupervisor()) {
            $goals = PerformanceGoal::whereHas('user', function($q) {
                        $q->where('department', $user->department);
                      })->with('user', 'assignedBy')->paginate(15);
        } else {
            $goals = $user->goals()->with('assignedBy')->paginate(15);
        }

        return view('goals.index', compact('goals'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        if (!Auth::user()->isSupervisor() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $users = User::byRole('employee')->get();
        return view('goals.create', compact('users'));
    }

    /**
     * Store goal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'target_value' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $validated['assigned_by'] = Auth::id();
        $validated['status'] = 'active';

        $goal = PerformanceGoal::create($validated);

        return redirect()->route('goals.show', $goal)->with('status', 'Goal created successfully!');
    }

    /**
     * Show goal details
     */
    public function show(PerformanceGoal $goal)
    {
        $goal->load('user', 'assignedBy', 'tracking', 'aiFormulations');
        return view('goals.show', compact('goal'));
    }

    /**
     * Show edit form
     */
    public function edit(PerformanceGoal $goal)
    {
        if (Auth::id() !== $goal->assigned_by && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('goals.edit', compact('goal'));
    }

    /**
     * Update goal
     */
    public function update(Request $request, PerformanceGoal $goal)
    {
        if (Auth::id() !== $goal->assigned_by && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'target_value' => 'required|integer|min:1',
            'status' => 'required|in:draft,active,completed,abandoned',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $goal->update($validated);

        return redirect()->route('goals.show', $goal)->with('status', 'Goal updated successfully!');
    }

    /**
     * Delete goal
     */
    public function destroy(PerformanceGoal $goal)
    {
        if (Auth::id() !== $goal->assigned_by && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $goal->delete();
        return redirect()->route('goals.index')->with('status', 'Goal deleted successfully!');
    }
}

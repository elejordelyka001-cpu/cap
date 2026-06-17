<?php

namespace App\Http\Controllers;

use App\Models\PerformanceGoal;
use App\Models\PerformanceTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceTrackingController extends Controller
{
    /**
     * Record progress update
     */
    public function store(Request $request, PerformanceGoal $goal)
    {
        $validated = $request->validate([
            'progress_value' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:on_track,at_risk,off_track,completed',
        ]);

        $tracking = PerformanceTracking::create([
            'goal_id' => $goal->id,
            'user_id' => $goal->user_id,
            'tracking_date' => now()->toDateString(),
            'progress_value' => $validated['progress_value'],
            'notes' => $validated['notes'],
            'status' => $validated['status'],
            'recorded_by' => Auth::id(),
        ]);

        // Update goal progress
        $goal->updateProgress($validated['progress_value']);

        return response()->json([
            'message' => 'Progress updated successfully!',
            'tracking' => $tracking,
        ]);
    }

    /**
     * Get tracking history for a goal
     */
    public function history(PerformanceGoal $goal)
    {
        $tracking = $goal->tracking()->with('recordedBy')->orderBy('tracking_date', 'desc')->get();
        return view('tracking.history', compact('goal', 'tracking'));
    }
}

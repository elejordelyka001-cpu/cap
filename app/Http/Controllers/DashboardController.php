<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PerformanceGoal;
use App\Models\PerformanceReview;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show main dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isSupervisor()) {
            return $this->supervisorDashboard();
        } else {
            return $this->employeeDashboard();
        }
    }

    /**
     * Admin Dashboard
     */
    private function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'total_goals' => PerformanceGoal::count(),
            'completed_goals' => PerformanceGoal::completed()->count(),
            'pending_reviews' => PerformanceReview::where('status', 'draft')->count(),
        ];

        $users = User::with('goals')->paginate(10);
        $recentGoals = PerformanceGoal::with('user')->latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'users', 'recentGoals'));
    }

    /**
     * Supervisor Dashboard
     */
    private function supervisorDashboard()
    {
        $supervisor = Auth::user();

        $stats = [
            'team_members' => User::where('department', $supervisor->department)
                                  ->where('id', '!=', $supervisor->id)
                                  ->count(),
            'active_goals' => PerformanceGoal::whereHas('user', function($q) {
                                    $q->where('department', Auth::user()->department);
                                  })->active()->count(),
            'pending_reviews' => PerformanceReview::where('reviewer_id', $supervisor->id)
                                                   ->where('status', 'draft')
                                                   ->count(),
            'completed_reviews' => PerformanceReview::where('reviewer_id', $supervisor->id)
                                                     ->where('status', 'completed')
                                                     ->count(),
        ];

        $teamMembers = User::where('department', $supervisor->department)
                          ->where('id', '!=', $supervisor->id)
                          ->with('goals')
                          ->paginate(10);

        $teamGoals = PerformanceGoal::whereHas('user', function($q) {
                                        $q->where('department', Auth::user()->department);
                                      })->with('user')->latest()->take(5)->get();

        return view('dashboard.supervisor', compact('stats', 'teamMembers', 'teamGoals'));
    }

    /**
     * Employee Dashboard
     */
    private function employeeDashboard()
    {
        $employee = Auth::user();

        $stats = [
            'total_goals' => $employee->goals()->count(),
            'active_goals' => $employee->goals()->active()->count(),
            'completed_goals' => $employee->goals()->completed()->count(),
            'average_progress' => $employee->goals()->avg('progress_percentage') ?? 0,
        ];

        $goals = $employee->goals()->with('tracking')->paginate(10);
        $recentReviews = $employee->reviews()->latest()->take(3)->get();

        return view('dashboard.employee', compact('stats', 'goals', 'recentReviews'));
    }
}

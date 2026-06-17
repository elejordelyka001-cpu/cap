<?php

namespace App\Http\Controllers;

use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerformanceReviewController extends Controller
{
    /**
     * List all reviews
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $reviews = PerformanceReview::with('user', 'reviewer')->paginate(15);
        } elseif ($user->isSupervisor()) {
            $reviews = PerformanceReview::where('reviewer_id', $user->id)
                                       ->orWhereHas('user', function($q) {
                                           $q->where('department', $user->department);
                                       })
                                       ->with('user', 'reviewer')
                                       ->paginate(15);
        } else {
            $reviews = $user->reviews()->with('reviewer')->paginate(15);
        }

        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show create review form
     */
    public function create()
    {
        if (!Auth::user()->isSupervisor() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $users = User::byRole('employee')->get();
        return view('reviews.create', compact('users'));
    }

    /**
     * Store review
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'review_period' => 'required|string',
            'review_type' => 'required|in:self,supervisor,peer,final',
            'overall_rating' => 'required|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'comments' => 'nullable|string',
        ]);

        $validated['reviewer_id'] = Auth::id();
        $validated['status'] = 'draft';

        $review = PerformanceReview::create($validated);

        return redirect()->route('reviews.show', $review)->with('status', 'Review created successfully!');
    }

    /**
     * Show review
     */
    public function show(PerformanceReview $review)
    {
        $review->load('user', 'reviewer');
        return view('reviews.show', compact('review'));
    }

    /**
     * Update review
     */
    public function update(Request $request, PerformanceReview $review)
    {
        if (Auth::id() !== $review->reviewer_id && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'strengths' => 'nullable|string',
            'areas_for_improvement' => 'nullable|string',
            'comments' => 'nullable|string',
            'status' => 'required|in:draft,submitted,approved,completed',
        ]);

        $review->update($validated);

        return redirect()->route('reviews.show', $review)->with('status', 'Review updated successfully!');
    }
}

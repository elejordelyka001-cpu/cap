@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3">Employee Dashboard</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Goals</h5>
                    <h2 class="mb-0">{{ $stats['total_goals'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Active Goals</h5>
                    <h2 class="mb-0">{{ $stats['active_goals'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed Goals</h5>
                    <h2 class="mb-0">{{ $stats['completed_goals'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Average Progress</h5>
                    <h2 class="mb-0">{{ round($stats['average_progress']) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- My Goals -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Performance Goals</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Goal</th>
                                    <th>Priority</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>End Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($goals as $goal)
                                <tr>
                                    <td>{{ Str::limit($goal->title, 50) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $goal->priority === 'critical' ? 'danger' : ($goal->priority === 'high' ? 'warning' : 'info') }}">
                                            {{ ucfirst($goal->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" style="width: {{ $goal->progress_percentage }}%">
                                                {{ $goal->progress_percentage }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $goal->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($goal->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $goal->end_date->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No goals assigned yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $goals->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reviews -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Performance Reviews</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Review Period</th>
                                    <th>Type</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Reviewer</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReviews as $review)
                                <tr>
                                    <td>{{ $review->review_period }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($review->review_type) }}</span>
                                    </td>
                                    <td>
                                        @if($review->overall_rating)
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->overall_rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $review->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($review->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $review->reviewer->name }}</td>
                                    <td>
                                        <a href="{{ route('reviews.show', $review) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No reviews yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

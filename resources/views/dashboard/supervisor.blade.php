@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h3 d-inline-block">Supervisor Dashboard</h1>
            <div class="float-end">
                <a href="{{ route('goals.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Assign Goal
                </a>
                <a href="{{ route('reviews.create') }}" class="btn btn-warning">
                    <i class="fas fa-star"></i> Create Review
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Team Members</h5>
                    <h2 class="mb-0">{{ $stats['team_members'] }}</h2>
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
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Reviews</h5>
                    <h2 class="mb-0">{{ $stats['pending_reviews'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed Reviews</h5>
                    <h2 class="mb-0">{{ $stats['completed_reviews'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Goals -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Team Performance Goals</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Goal</th>
                                    <th>Priority</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamGoals as $goal)
                                <tr>
                                    <td>{{ $goal->user->name }}</td>
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
                                    <td>
                                        <a href="{{ route('goals.show', $goal) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Team Members -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Team Members</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Position</th>
                                    <th>Active Goals</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teamMembers as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>{{ $member->position }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $member->goals->where('status', 'active')->count() }}</span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewMember{{ $member->id }}">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No team members</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $teamMembers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Analytics')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Analytics</li>
@endsection

@section('page-header')
    <div>
        <h1 class="h2 fw-bold mb-0">Analytics</h1>
        <p class="text-muted mb-0">System statistics and insights</p>
    </div>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Total Users</p>
                        <h3 class="fw-bold mb-0">{{ $analytics['total_users'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Students</p>
                        <h3 class="fw-bold mb-0">{{ $analytics['total_students'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="bi bi-mortarboard text-info" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Lecturers</p>
                        <h3 class="fw-bold mb-0">{{ $analytics['total_lecturers'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="bi bi-book text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Staff</p>
                        <h3 class="fw-bold mb-0">{{ $analytics['total_staff'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="bi bi-briefcase text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-bar-chart me-2"></i>User Status Distribution
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Active Users</span>
                            <strong>{{ $analytics['active_users'] ?? 0 }}</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ ($analytics['active_users'] ?? 0) / max(($analytics['total_users'] ?? 1), 1) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Inactive Users</span>
                            <strong>{{ $analytics['inactive_users'] ?? 0 }}</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                 style="width: {{ ($analytics['inactive_users'] ?? 0) / max(($analytics['total_users'] ?? 1), 1) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Suspended Users</span>
                            <strong>{{ $analytics['suspended_users'] ?? 0 }}</strong>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: {{ ($analytics['suspended_users'] ?? 0) / max(($analytics['total_users'] ?? 1), 1) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-activity me-2"></i>Activity Summary
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                        <span class="text-muted"><i class="bi bi-plus-circle text-success me-2"></i>Created</span>
                        <strong>{{ $activitySummary['created'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                        <span class="text-muted"><i class="bi bi-pencil-square text-info me-2"></i>Updated</span>
                        <strong>{{ $activitySummary['updated'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                        <span class="text-muted"><i class="bi bi-trash text-danger me-2"></i>Deleted</span>
                        <strong>{{ $activitySummary['deleted'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                        <span class="text-muted"><i class="bi bi-box-arrow-in-right text-primary me-2"></i>Login</span>
                        <strong>{{ $activitySummary['login'] ?? 0 }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-star me-2"></i>Top Active Users
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Activities</th>
                            <th class="text-end">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topActiveUsers ?? [] as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ $user->user->name ?? 'User' }}" 
                                             alt="avatar" class="rounded-circle" width="32" height="32">
                                        <span>{{ $user->user->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->user->email ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $user->activity_count ?? 0 }}</span>
                                </td>
                                <td class="text-end">
                                    {{ round(($user->activity_count ?? 0) / max(($analytics['total_activities'] ?? 1), 1) * 100, 2) }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    No activity data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
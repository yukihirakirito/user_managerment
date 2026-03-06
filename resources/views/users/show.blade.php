@extends('layouts.app')

@section('title', 'User - ' . ($user->name ?? ''))

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $user->name ?? 'User' }}</li>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        {{-- User Details Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person-fill me-2"></i>User Information
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('users.edit', $user ?? 0) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('users.destroy', $user ?? 0) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this user?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <img src="https://ui-avatars.com/api/?name={{ $user->name ?? 'User' }}&background=random&size=200" 
                             alt="{{ $user->name ?? 'User' }}" class="img-fluid rounded-circle mb-3" width="150">
                        <p class="text-muted small">Member since {{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-9">
                        <h3 class="fw-bold">{{ $user->name ?? 'N/A' }}</h3>
                        <p class="text-muted mb-3">
                            <span class="badge bg-{{ $user->user_type === 'student' ? 'info' : ($user->user_type === 'lecturer' ? 'success' : 'warning') }}">
                                {{ ucfirst($user->user_type ?? '') }} Account
                            </span>
                            <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($user->status ?? '') }}
                            </span>
                        </p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Email:</small>
                                <p class="fw-500">
                                    <a href="mailto:{{ $user->email ?? '' }}">{{ $user->email ?? 'N/A' }}</a>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Phone:</small>
                                <p class="fw-500">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Created:</small>
                                <p class="fw-500">{{ $user->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Updated:</small>
                                <p class="fw-500">{{ $user->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Type-Specific Details --}}
        @if($user->user_type === 'student' && $user->student)
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-mortarboard me-2"></i>Student Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Student Code:</small>
                            <p class="fw-500">{{ $user->student->student_code ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Major:</small>
                            <p class="fw-500">{{ $user->student->major ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Enrollment Date:</small>
                            <p class="fw-500">{{ $user->student->enrollment_date ? $user->student->enrollment_date->format('M d, Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Graduation Date:</small>
                            <p class="fw-500">{{ $user->student->graduation_date ? $user->student->graduation_date->format('M d, Y') : 'Not set' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">GPA:</small>
                            <p class="fw-500">{{ $user->student->gpa ?? 'Not set' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($user->user_type === 'lecturer' && $user->lecturer)
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-book me-2"></i>Lecturer Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Employee Code:</small>
                            <p class="fw-500">{{ $user->lecturer->employee_code ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Department:</small>
                            <p class="fw-500">{{ $user->lecturer->department ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Specialization:</small>
                            <p class="fw-500">{{ $user->lecturer->specialization ?? 'Not set' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Academic Degree:</small>
                            <p class="fw-500">{{ $user->lecturer->academic_degree ?? 'Not set' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Hire Date:</small>
                            <p class="fw-500">{{ $user->lecturer->hire_date ? $user->lecturer->hire_date->format('M d, Y') : 'Not set' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($user->user_type === 'staff' && $user->staff)
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-briefcase me-2"></i>Staff Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Employee Code:</small>
                            <p class="fw-500">{{ $user->staff->employee_code ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Department:</small>
                            <p class="fw-500">{{ $user->staff->department ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Position:</small>
                            <p class="fw-500">{{ $user->staff->position ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Employment Type:</small>
                            <p class="fw-500">{{ ucfirst($user->staff->employment_type ?? 'N/A') }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Hire Date:</small>
                            <p class="fw-500">{{ $user->staff->hire_date ? $user->staff->hire_date->format('M d, Y') : 'Not set' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Quick Actions Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('users.edit', $user ?? 0) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>Edit User
                    </a>
                    <button class="btn btn-secondary">
                        <i class="bi bi-key me-2"></i>Reset Password
                    </button>
                    @if($user->status === 'active')
                        <form method="POST" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-pause-circle me-2"></i>Suspend User
                            </button>
                        </form>
                    @else
                        <form method="POST" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-play-circle me-2"></i>Activate User
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Activity Card --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-activity me-2"></i>Activity
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Created</small>
                    <p class="text-sm fw-500">{{ $user->created_at->format('M d, Y') }} at {{ $user->created_at->format('H:i') }}</p>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Last Updated</small>
                    <p class="text-sm fw-500">{{ $user->updated_at->format('M d, Y') }} at {{ $user->updated_at->format('H:i') }}</p>
                </div>

                <hr>

                <div>
                    <small class="text-muted d-block">Account ID</small>
                    <p class="text-sm text-monospace">{{ $user->id ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
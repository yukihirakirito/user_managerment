@extends('layouts.app')

@section('title', 'User Management')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Users</li>
@endsection

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-0">User Management</h1>
            <p class="text-muted mb-0">Manage students, lecturers, and staff members</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add New User
        </a>
    </div>
@endsection

@section('content')
<div class="row g-4">
    {{-- Filter & Search Card --}}
    <div class="col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-funnel me-2"></i>Filters
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.index') }}" method="GET">
                    {{-- Search --}}
                    <div class="mb-3">
                        <label for="search" class="form-label fw-500">Search</label>
                        <input type="text" id="search" name="search" 
                               class="form-control form-control-sm" 
                               placeholder="Name, email, phone..."
                               value="{{ request('search') }}">
                    </div>

                    {{-- User Type --}}
                    <div class="mb-3">
                        <label for="user_type" class="form-label fw-500">User Type</label>
                        <select id="user_type" name="user_type" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            <option value="student" {{ request('user_type') === 'student' ? 'selected' : '' }}>
                                Student
                            </option>
                            <option value="lecturer" {{ request('user_type') === 'lecturer' ? 'selected' : '' }}>
                                Lecturer
                            </option>
                            <option value="staff" {{ request('user_type') === 'staff' ? 'selected' : '' }}>
                                Staff
                            </option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="status" class="form-label fw-500">Status</label>
                        <select id="status" name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>
                                Suspended
                            </option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search me-2"></i>Filter
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Users Table Card --}}
    <div class="col-lg-9">
        <div class="card shadow-sm">
            {{-- Table Header --}}
            <div class="card-header bg-light border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-table me-2"></i>Users List
                    </h5>
                    <small class="text-muted">Total records</small>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th style="width: 120px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users ?? [] as $user)
                            <tr class="align-middle">
                                <td>
                                    <input type="checkbox" class="form-check-input user-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name={{ $user->name }}&background=random" 
                                             alt="{{ $user->name }}" 
                                             class="rounded-circle" 
                                             width="40" height="40">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->phone ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $user->email }}" class="text-primary text-decoration-none">
                                        {{ $user->email }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->user_type === 'student' ? 'info' : ($user->user_type === 'lecturer' ? 'success' : 'warning') }}">
                                        {{ ucfirst($user->user_type) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('users.show', $user) }}" 
                                           class="btn btn-outline-info" 
                                           title="View" 
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" 
                                           class="btn btn-outline-warning" 
                                           title="Edit" 
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger" 
                                                    title="Delete" 
                                                    data-bs-toggle="tooltip">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                        <p class="mt-3">No users found. <a href="{{ route('users.create') }}">Create one</a></p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users ?? null)
            <div class="card-footer bg-light">
                <nav aria-label="Page navigation">
                    {{ $users->links('pagination::bootstrap-5') }}
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Select all checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Initialize tooltips
const tooltips = new bootstrap.Tooltip(document.body, {
    selector: '[data-bs-toggle="tooltip"]'
});
</script>
@endpush
@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-header')
    <div>
        <h1 class="h2 fw-bold mb-0">Dashboard</h1>
        <p class="text-muted mb-0">Welcome back! Here's your activity overview.</p>
    </div>
@endsection

@section('content')
{{-- Statistics Cards --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Total Users</p>
                        <h3 class="fw-bold mb-0">{{ $totalUsers ?? 0 }}</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="bi bi-people text-primary" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <small class="text-success">+5% from last month</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Students</p>
                        <h3 class="fw-bold mb-0">{{ $totalStudents ?? 0 }}</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="bi bi-mortarboard text-info" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <small class="text-muted">Active accounts</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Lecturers</p>
                        <h3 class="fw-bold mb-0">{{ $totalLecturers ?? 0 }}</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="bi bi-book text-success" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <small class="text-muted">Teaching staff</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Staff</p>
                        <h3 class="fw-bold mb-0">{{ $totalStaff ?? 0 }}</h3>
                    </div>
                    <div class="bg-light rounded-circle p-3">
                        <i class="bi bi-briefcase text-warning" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <small class="text-muted">Support staff</small>
            </div>
        </div>
    </div>
</div>

{{-- Recent Users Table --}}
<div class="card shadow-sm">
    <div class="card-header bg-light border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-clock-history me-2"></i>Recent Users
            </h5>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-primary">View All</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Add dynamic data here from controller --}}
                <tr class="align-middle">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name=Sample+User" class="rounded-circle" width="32" height="32">
                            <span>Sample User</span>
                        </div>
                    </td>
                    <td>sample@example.com</td>
                    <td><span class="badge bg-info">Student</span></td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td>Jan 15, 2024</td>
                    <td class="text-end">
                        <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
@extends('layouts.app')

@section('title', 'Edit User - ' . ($user->name ?? ''))

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
@endsection

@section('page-header')
    <div>
        <h1 class="h2 fw-bold mb-0">Edit User</h1>
        <p class="text-muted mb-0">Update user information and details</p>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>User Information
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('users.update', $user ?? 0) }}" method="POST" id="editForm">
                    @csrf
                    @method('PUT')

                    {{-- User Type Display --}}
                    <div class="mb-4 p-3 bg-light rounded-3">
                        <label class="form-label fw-bold">User Type</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ $user->user_type === 'student' ? 'info' : ($user->user_type === 'lecturer' ? 'success' : 'warning') }}">
                                {{ ucfirst($user->user_type ?? '') }}
                            </span>
                        </p>
                        <small class="text-muted">User type cannot be changed</small>
                    </div>

                    {{-- Basic Information --}}
                    <fieldset class="border rounded-3 p-3 mb-4" style="border-color: #e5e7eb !important;">
                        <legend class="float-none w-auto ps-2">
                            <i class="bi bi-person-fill"></i> Basic Information
                        </legend>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $user->name ?? '') }}" 
                                       placeholder="John Doe" required>
                                @error('name')
                                    <div class="form-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email ?? '') }}" 
                                       placeholder="john@example.com" required>
                                @error('email')
                                    <div class="form-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone', $user->phone ?? '') }}" placeholder="+1 (555) 123-4567">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status', $user->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $user->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ old('status', $user->status ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                    <div class="form-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- Password Change Section --}}
                    <fieldset class="border rounded-3 p-3 mb-4" style="border-color: #e5e7eb !important;">
                        <legend class="float-none w-auto ps-2">
                            <i class="bi bi-lock-fill"></i> Change Password (Optional)
                        </legend>

                        <p class="text-muted small mb-3">Leave blank to keep current password</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Minimum 8 characters">
                                @error('password')
                                    <div class="form-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" 
                                       name="password_confirmation" placeholder="Confirm password">
                            </div>
                        </div>
                    </fieldset>

                    {{-- Type-Specific Information --}}
                    {{-- Student Info --}}
                    @if($user->user_type === 'student' && $user->student)
                    <fieldset class="border rounded-3 p-3 mb-4" style="border-color: #e5e7eb !important;">
                        <legend class="float-none w-auto ps-2">
                            <i class="bi bi-mortarboard-fill"></i> Student Information
                        </legend>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="student_code" class="form-label">Student Code</label>
                                <input type="text" class="form-control" id="student_code" name="student_code" 
                                       value="{{ old('student_code', $user->student->student_code ?? '') }}" placeholder="ST00001">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="major" class="form-label">Major</label>
                                <input type="text" class="form-control" id="major" name="major" 
                                       value="{{ old('major', $user->student->major ?? '') }}" placeholder="Computer Science">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="enrollment_date" class="form-label">Enrollment Date</label>
                                <input type="date" class="form-control" id="enrollment_date" name="enrollment_date" 
                                       value="{{ old('enrollment_date', $user->student->enrollment_date ?? '') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="graduation_date" class="form-label">Graduation Date</label>
                                <input type="date" class="form-control" id="graduation_date" name="graduation_date" 
                                       value="{{ old('graduation_date', $user->student->graduation_date ?? '') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gpa" class="form-label">GPA</label>
                                <input type="number" class="form-control" id="gpa" name="gpa" 
                                       value="{{ old('gpa', $user->student->gpa ?? '') }}" placeholder="0.00" min="0" max="4" step="0.01">
                            </div>
                        </div>
                    </fieldset>
                    @endif

                    {{-- Lecturer Info --}}
                    @if($user->user_type === 'lecturer' && $user->lecturer)
                    <fieldset class="border rounded-3 p-3 mb-4" style="border-color: #e5e7eb !important;">
                        <legend class="float-none w-auto ps-2">
                            <i class="bi bi-book-fill"></i> Lecturer Information
                        </legend>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="employee_code" class="form-label">Employee Code</label>
                                <input type="text" class="form-control" id="employee_code" name="employee_code" 
                                       value="{{ old('employee_code', $user->lecturer->employee_code ?? '') }}" placeholder="EMP001">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department" name="department" 
                                       value="{{ old('department', $user->lecturer->department ?? '') }}" placeholder="Computer Science">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control" id="specialization" name="specialization" 
                                       value="{{ old('specialization', $user->lecturer->specialization ?? '') }}" placeholder="AI & Machine Learning">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="academic_degree" class="form-label">Academic Degree</label>
                                <select class="form-select" id="academic_degree" name="academic_degree">
                                    <option value="">Select degree</option>
                                    <option value="Associate" {{ old('academic_degree', $user->lecturer->academic_degree ?? '') === 'Associate' ? 'selected' : '' }}>Associate</option>
                                    <option value="Bachelor" {{ old('academic_degree', $user->lecturer->academic_degree ?? '') === 'Bachelor' ? 'selected' : '' }}>Bachelor</option>
                                    <option value="Master" {{ old('academic_degree', $user->lecturer->academic_degree ?? '') === 'Master' ? 'selected' : '' }}>Master</option>
                                    <option value="PhD" {{ old('academic_degree', $user->lecturer->academic_degree ?? '') === 'PhD' ? 'selected' : '' }}>PhD</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="hire_date" class="form-label">Hire Date</label>
                                <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                       value="{{ old('hire_date', $user->lecturer->hire_date ?? '') }}">
                            </div>
                        </div>
                    </fieldset>
                    @endif

                    {{-- Staff Info --}}
                    @if($user->user_type === 'staff' && $user->staff)
                    <fieldset class="border rounded-3 p-3 mb-4" style="border-color: #e5e7eb !important;">
                        <legend class="float-none w-auto ps-2">
                            <i class="bi bi-briefcase-fill"></i> Staff Information
                        </legend>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="emp_code" class="form-label">Employee Code</label>
                                <input type="text" class="form-control" id="emp_code" name="employee_code" 
                                       value="{{ old('employee_code', $user->staff->employee_code ?? '') }}" placeholder="STAFF001">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="dept" class="form-label">Department</label>
                                <input type="text" class="form-control" id="dept" name="department" 
                                       value="{{ old('department', $user->staff->department ?? '') }}" placeholder="Administration">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control" id="position" name="position" 
                                       value="{{ old('position', $user->staff->position ?? '') }}" placeholder="Manager">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="employment_type" class="form-label">Employment Type</label>
                                <select class="form-select" id="employment_type" name="employment_type">
                                    <option value="full-time" {{ old('employment_type', $user->staff->employment_type ?? '') === 'full-time' ? 'selected' : '' }}>Full-time</option>
                                    <option value="part-time" {{ old('employment_type', $user->staff->employment_type ?? '') === 'part-time' ? 'selected' : '' }}>Part-time</option>
                                    <option value="contract" {{ old('employment_type', $user->staff->employment_type ?? '') === 'contract' ? 'selected' : '' }}>Contract</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="staff_hire_date" class="form-label">Hire Date</label>
                                <input type="date" class="form-control" id="staff_hire_date" name="hire_date" 
                                       value="{{ old('hire_date', $user->staff->hire_date ?? '') }}">
                            </div>
                        </div>
                    </fieldset>
                    @endif

                    {{-- Form Actions --}}
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update User
                        </button>
                        <a href="{{ route('users.show', $user ?? 0) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
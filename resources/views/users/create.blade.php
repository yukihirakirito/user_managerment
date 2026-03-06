@extends('layouts.app')

@section('title', 'Create New User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
@endsection

@section('page-header')
    <div>
        <h1 class="h2 fw-bold mb-0">Create New User</h1>
        <p class="text-muted mb-0">Add a new student, lecturer, or staff member</p>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-person-plus me-2"></i>User Information
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST" id="userForm">
                    @csrf

                    {{-- User Type Selection --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">User Type *</label>
                            <div class="btn-group d-flex" role="group">
                                <input type="radio" class="btn-check" name="user_type" id="type_student" 
                                       value="student" required {{ old('user_type') === 'student' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary flex-fill" for="type_student">
                                    <i class="bi bi-mortarboard"></i> Student
                                </label>

                                <input type="radio" class="btn-check" name="user_type" id="type_lecturer" 
                                       value="lecturer" {{ old('user_type') === 'lecturer' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary flex-fill" for="type_lecturer">
                                    <i class="bi bi-book"></i> Lecturer
                                </label>

                                <input type="radio" class="btn-check" name="user_type" id="type_staff" 
                                       value="staff" {{ old('user_type') === 'staff' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary flex-fill" for="type_staff">
                                    <i class="bi bi-briefcase"></i> Staff
                                </label>
                            </div>
                            @error('user_type')
                                <div class="form-feedback" style="display: block;">{{ $message }}</div>
                            @enderror
                        </div>
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
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="John Doe" required>
                                @error('name')
                                    <div class="form-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="john@example.com" required>
                                @error('email')
                                    <div class="form-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" 
                                       placeholder="Minimum 8 characters" required>
                                @error('password')
                                    <div class="form-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="{{ old('phone') }}" placeholder="+1 (555) 123-4567">
                            </div>
                        </div>
                    </fieldset>

                    {{-- Type-Specific Information --}}
                    <div id="type-specific-info">
                        {{-- Student Fields --}}
                        <fieldset id="student-info" class="border rounded-3 p-3 mb-4" style="border-color: #e5e7eb !important; display: none;">
                            <legend class="float-none w-auto ps-2">
                                <i class="bi bi-mortarboard-fill"></i> Student Information
                            </legend>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="student_code" class="form-label">Student Code *</label>
                                    <input type="text" class="form-control" id="student_code" name="student_code" 
                                           value="{{ old('student_code') }}" placeholder="ST00001">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="major" class="form-label">Major *</label>
                                    <input type="text" class="form-control" id="major" name="major" 
                                           value="{{ old('major') }}" placeholder="Computer Science">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="enrollment_date" class="form-label">Enrollment Date *</label>
                                    <input type="date" class="form-control" id="enrollment_date" name="enrollment_date" 
                                           value="{{ old('enrollment_date') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="graduation_date" class="form-label">Graduation Date</label>
                                    <input type="date" class="form-control" id="graduation_date" name="graduation_date" 
                                           value="{{ old('graduation_date') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gpa" class="form-label">GPA</label>
                                    <input type="number" class="form-control" id="gpa" name="gpa" 
                                           value="{{ old('gpa') }}" placeholder="0.00" min="0" max="4" step="0.01">
                                </div>
                            </div>
                        </fieldset>

                        {{-- Lecturer Fields --}}
                        <fieldset id="lecturer-info" class="border rounded-3 p-3 mb-4" style="border-color: #e5e7eb !important; display: none;">
                            <legend class="float-none w-auto ps-2">
                                <i class="bi bi-book-fill"></i> Lecturer Information
                            </legend>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="employee_code" class="form-label">Employee Code *</label>
                                    <input type="text" class="form-control" id="employee_code" name="employee_code" 
                                           value="{{ old('employee_code') }}" placeholder="EMP001">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="department" class="form-label">Department *</label>
                                    <input type="text" class="form-control" id="department" name="department" 
                                           value="{{ old('department') }}" placeholder="Computer Science">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="specialization" class="form-label">Specialization</label>
                                    <input type="text" class="form-control" id="specialization" name="specialization" 
                                           value="{{ old('specialization') }}" placeholder="AI & Machine Learning">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="academic_degree" class="form-label">Academic Degree</label>
                                    <select class="form-select" id="academic_degree" name="academic_degree">
                                        <option value="">Select degree</option>
                                        <option value="Associate" {{ old('academic_degree') === 'Associate' ? 'selected' : '' }}>Associate</option>
                                        <option value="Bachelor" {{ old('academic_degree') === 'Bachelor' ? 'selected' : '' }}>Bachelor</option>
                                        <option value="Master" {{ old('academic_degree') === 'Master' ? 'selected' : '' }}>Master</option>
                                        <option value="PhD" {{ old('academic_degree') === 'PhD' ? 'selected' : '' }}>PhD</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="hire_date_lecturer" class="form-label">Hire Date *</label>
                                    <input type="date" class="form-control" id="hire_date_lecturer" name="hire_date_lecturer" 
                                           value="{{ old('hire_date_lecturer') }}">
                                </div>
                            </div>
                        </fieldset>

                        {{-- Staff Fields --}}
                        <fieldset id="staff-info" class="border rounded-3 p-3 mb-4" style="border-color: #e5e7eb !important; display: none;">
                            <legend class="float-none w-auto ps-2">
                                <i class="bi bi-briefcase-fill"></i> Staff Information
                            </legend>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="employee_code_staff" class="form-label">Employee Code *</label>
                                    <input type="text" class="form-control" id="employee_code_staff" name="employee_code_staff" 
                                           value="{{ old('employee_code_staff') }}" placeholder="STAFF001">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="department_staff" class="form-label">Department *</label>
                                    <input type="text" class="form-control" id="department_staff" name="department_staff" 
                                           value="{{ old('department_staff') }}" placeholder="Administration">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="position" class="form-label">Position *</label>
                                    <input type="text" class="form-control" id="position" name="position" 
                                           value="{{ old('position') }}" placeholder="Manager">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="employment_type" class="form-label">Employment Type</label>
                                    <select class="form-select" id="employment_type" name="employment_type">
                                        <option value="full-time" {{ old('employment_type') === 'full-time' ? 'selected' : '' }}>Full-time</option>
                                        <option value="part-time" {{ old('employment_type') === 'part-time' ? 'selected' : '' }}>Part-time</option>
                                        <option value="contract" {{ old('employment_type') === 'contract' ? 'selected' : '' }}>Contract</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="hire_date_staff" class="form-label">Hire Date *</label>
                                    <input type="date" class="form-control" id="hire_date_staff" name="hire_date_staff" 
                                           value="{{ old('hire_date_staff') }}">
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    {{-- Form Actions --}}
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Create User
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Handle user type selection
document.querySelectorAll('input[name="user_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        // Hide all type-specific info
        document.getElementById('student-info').style.display = 'none';
        document.getElementById('lecturer-info').style.display = 'none';
        document.getElementById('staff-info').style.display = 'none';

        // Show selected type info
        if (this.value === 'student') {
            document.getElementById('student-info').style.display = 'block';
        } else if (this.value === 'lecturer') {
            document.getElementById('lecturer-info').style.display = 'block';
        } else if (this.value === 'staff') {
            document.getElementById('staff-info').style.display = 'block';
        }
    });
});

// Trigger change on page load
document.querySelector('input[name="user_type"]:checked')?.dispatchEvent(new Event('change'));
</script>
@endpush
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users with filtering and search
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search filter - search by name, email, phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // User type filter
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->input('user_type'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Pagination
        $users = $query->latest()->paginate(15);

        return view('users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Get validation rules based on user type
        $rules = $this->getValidationRules($request->input('user_type'));
        
        // Validate input
        $validated = $request->validate($rules, $this->getValidationMessages());

        try {
            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'user_type' => $validated['user_type'],
                'status' => 'active',
            ]);

            // Create type-specific record
            $this->createTypeSpecificRecord($user, $validated);

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User created successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error creating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified user
     *
     * @param \App\Models\User $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user
     *
     * @param \App\Models\User $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user in storage
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in('active', 'inactive', 'suspended')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            
            // Student fields
            'student_code' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'enrollment_date' => ['nullable', 'date'],
            'graduation_date' => ['nullable', 'date'],
            'gpa' => ['nullable', 'numeric', 'between:0,4'],
            
            // Lecturer fields
            'employee_code' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'academic_degree' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            
            // Staff fields
            'position' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', Rule::in('full-time', 'part-time', 'contract')],
        ], $this->getValidationMessages());

        try {
            // Update user
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $user->phone,
                'status' => $validated['status'],
            ]);

            // Update password if provided
            if (!empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            // Update type-specific record
            $this->updateTypeSpecificRecord($user, $validated);

            return redirect()
                ->route('users.show', $user)
                ->with('success', 'User updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error updating user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user from storage
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        try {
            $name = $user->name;
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', "User '{$name}' deleted successfully!");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Change user status (AJAX)
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in('active', 'inactive', 'suspended')],
        ]);

        try {
            $user->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => "User status changed to {$validated['status']}",
                'status' => $validated['status'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get users by type (API)
     *
     * @param string $type
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByType(Request $request, $type = null)
    {
        // Support both route parameter and query parameter
        $userType = $type ?? $request->input('type');

        if (!$userType) {
            return response()->json(['error' => 'User type is required'], 400);
        }

        $users = User::where('user_type', $userType)
            ->paginate(15)
            ->toArray();

        return response()->json($users);
    }

    /**
     * Search users (API)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $search = $request->input('q');

        if (strlen($search) < 2) {
            return response()->json(['error' => 'Search term must be at least 2 characters'], 400);
        }

        $users = User::where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        })
        ->limit(10)
        ->get();

        return response()->json($users);
    }

    /**
     * Export users data (API)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $query = User::query();

        // Apply filters if provided
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->input('user_type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->get();

        return response()->json([
            'export_date' => now()->format('Y-m-d H:i:s'),
            'total_users' => $users->count(),
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'type' => ucfirst($user->user_type),
                    'status' => ucfirst($user->status),
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    /**
     * Get user statistics (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        return response()->json([
            'total_users' => User::count(),
            'total_students' => User::where('user_type', 'student')->count(),
            'total_lecturers' => User::where('user_type', 'lecturer')->count(),
            'total_staff' => User::where('user_type', 'staff')->count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'suspended_users' => User::where('status', 'suspended')->count(),
        ]);
    }

    /**
     * Get validation rules based on user type
     *
     * @param string $userType
     * @return array
     */
    private function getValidationRules($userType)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'user_type' => ['required', Rule::in('student', 'lecturer', 'staff')],
        ];

        // Add type-specific rules
        if ($userType === 'student') {
            $rules['student_code'] = ['required', 'string', 'unique:students'];
            $rules['major'] = ['required', 'string', 'max:255'];
            $rules['enrollment_date'] = ['required', 'date'];
            $rules['graduation_date'] = ['nullable', 'date'];
            $rules['gpa'] = ['nullable', 'numeric', 'between:0,4'];
        } elseif ($userType === 'lecturer') {
            $rules['employee_code'] = ['required', 'string', 'unique:lecturers'];
            $rules['department'] = ['required', 'string', 'max:255'];
            $rules['specialization'] = ['nullable', 'string', 'max:255'];
            $rules['academic_degree'] = ['nullable', 'string', 'max:255'];
            $rules['hire_date_lecturer'] = ['required', 'date'];
        } elseif ($userType === 'staff') {
            $rules['employee_code_staff'] = ['required', 'string', 'unique:staffs'];
            $rules['department_staff'] = ['required', 'string', 'max:255'];
            $rules['position'] = ['required', 'string', 'max:255'];
            $rules['employment_type'] = ['nullable', Rule::in('full-time', 'part-time', 'contract')];
            $rules['hire_date_staff'] = ['required', 'date'];
        }

        return $rules;
    }

    /**
     * Get validation messages
     *
     * @return array
     */
    private function getValidationMessages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name must not exceed 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'The email address already exists.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'user_type.required' => 'Please select a user type.',
            'student_code.required' => 'The student code is required.',
            'student_code.unique' => 'The student code already exists.',
            'major.required' => 'The major is required.',
            'enrollment_date.required' => 'The enrollment date is required.',
            'employee_code.unique' => 'The employee code already exists.',
            'department.required' => 'The department is required.',
        ];
    }

    /**
     * Create type-specific record when user is created
     *
     * @param \App\Models\User $user
     * @param array $data
     * @return void
     */
    private function createTypeSpecificRecord(User $user, array $data)
    {
        if ($data['user_type'] === 'student') {
            Student::create([
                'user_id' => $user->id,
                'student_code' => $data['student_code'],
                'major' => $data['major'],
                'enrollment_date' => $data['enrollment_date'],
                'graduation_date' => $data['graduation_date'] ?? null,
                'gpa' => $data['gpa'] ?? null,
            ]);
        } elseif ($data['user_type'] === 'lecturer') {
            Lecturer::create([
                'user_id' => $user->id,
                'employee_code' => $data['employee_code'],
                'department' => $data['department'],
                'specialization' => $data['specialization'] ?? null,
                'academic_degree' => $data['academic_degree'] ?? null,
                'hire_date' => $data['hire_date_lecturer'] ?? null,
            ]);
        } elseif ($data['user_type'] === 'staff') {
            Staff::create([
                'user_id' => $user->id,
                'employee_code' => $data['employee_code_staff'],
                'department' => $data['department_staff'],
                'position' => $data['position'],
                'employment_type' => $data['employment_type'] ?? 'full-time',
                'hire_date' => $data['hire_date_staff'] ?? null,
            ]);
        }
    }

    /**
     * Update type-specific record when user is updated
     *
     * @param \App\Models\User $user
     * @param array $data
     * @return void
     */
    private function updateTypeSpecificRecord(User $user, array $data)
    {
        if ($user->user_type === 'student' && $user->student) {
            $user->student->update([
                'student_code' => $data['student_code'] ?? $user->student->student_code,
                'major' => $data['major'] ?? $user->student->major,
                'enrollment_date' => $data['enrollment_date'] ?? $user->student->enrollment_date,
                'graduation_date' => $data['graduation_date'] ?? $user->student->graduation_date,
                'gpa' => $data['gpa'] ?? $user->student->gpa,
            ]);
        } elseif ($user->user_type === 'lecturer' && $user->lecturer) {
            $user->lecturer->update([
                'employee_code' => $data['employee_code'] ?? $user->lecturer->employee_code,
                'department' => $data['department'] ?? $user->lecturer->department,
                'specialization' => $data['specialization'] ?? $user->lecturer->specialization,
                'academic_degree' => $data['academic_degree'] ?? $user->lecturer->academic_degree,
                'hire_date' => $data['hire_date'] ?? $user->lecturer->hire_date,
            ]);
        } elseif ($user->user_type === 'staff' && $user->staff) {
            $user->staff->update([
                'employee_code' => $data['employee_code'] ?? $user->staff->employee_code,
                'department' => $data['department'] ?? $user->staff->department,
                'position' => $data['position'] ?? $user->staff->position,
                'employment_type' => $data['employment_type'] ?? $user->staff->employment_type,
                'hire_date' => $data['hire_date'] ?? $user->staff->hire_date,
            ]);
        }
    }
}
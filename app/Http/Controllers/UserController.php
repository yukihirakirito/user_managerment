<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Constructor - Dependency Injection
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of users with filtering and search
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get filters from request
        $filters = [
            'search' => $request->input('search'),
            'user_type' => $request->input('user_type'),
            'status' => $request->input('status'),
        ];

        // Use repository to get filtered users
        $users = $this->userRepository->filterUsers($filters, 15);

        // Log activity
        $this->logActivity('viewed_users');

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
        $rules = $this->getValidationRules($request->input('user_type'));
        
        $validated = $request->validate($rules, $this->getValidationMessages());

        try {
            if ($validated['user_type'] === 'student') {
                $user = $this->userRepository->createStudent($validated);
            } elseif ($validated['user_type'] === 'lecturer') {
                $user = $this->userRepository->createLecturer($validated);
            } elseif ($validated['user_type'] === 'staff') {
                $user = $this->userRepository->createStaff($validated);
            } else {
                $user = $this->userRepository->create($validated);
            }

            $this->logActivity('created_user', ['user_id' => $user->id]);

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
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = $this->userRepository->getUserWithRelations($id);

        if (!$user) {
            abort(404, 'User not found');
        }

        $this->logActivity('viewed_user', ['user_id' => $user->id]);

        return view('users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified user
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            abort(404, 'User not found');
        }

        return view('users.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified user in storage
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            abort(404, 'User not found');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', \Illuminate\Validation\Rule::in('active', 'inactive', 'suspended')],
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
            'employment_type' => ['nullable', \Illuminate\Validation\Rule::in('full-time', 'part-time', 'contract')],
        ], $this->getValidationMessages());

        try {
            $this->userRepository->update($id, $validated);

            $this->logActivity('updated_user', ['user_id' => $id, 'changes' => $validated]);

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
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            abort(404, 'User not found');
        }

        try {
            $name = $user->name;

            $this->userRepository->delete($id);

            $this->logActivity('deleted_user', ['user_name' => $name]);

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
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => ['required', \Illuminate\Validation\Rule::in('active', 'inactive', 'suspended')],
        ]);

        try {
            $user = $this->userRepository->updateStatus($id, $validated['status']);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            // Log activity
            $this->logActivity('changed_user_status', ['user_id' => $id, 'status' => $validated['status']]);

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
     * @param Request $request
     * @param string|null $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByType(Request $request, $type = null)
    {
        $userType = $type ?? $request->input('type');

        if (!$userType) {
            return response()->json(['error' => 'User type is required'], 400);
        }

        $users = $this->userRepository->getUsersByType($userType, 15);

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

        $users = $this->userRepository->searchUsers($search);

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
        $filters = [
            'user_type' => $request->input('user_type'),
            'status' => $request->input('status'),
        ];

        $users = $this->userRepository->getForExport($filters);

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
        $stats = $this->userRepository->getStatistics();

        return response()->json($stats);
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
            'user_type' => ['required', \Illuminate\Validation\Rule::in('student', 'lecturer', 'staff')],
        ];

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
            $rules['employment_type'] = ['nullable', \Illuminate\Validation\Rule::in('full-time', 'part-time', 'contract')];
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
     * Log activity
     *
     * @param string $action
     * @param array $data
     * @return void
     */
    private function logActivity($action, $data = [])
    {
        try {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model' => 'User',
                'changes' => !empty($data) ? $data : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}
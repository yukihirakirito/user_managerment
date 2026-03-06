<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class StaffController extends Controller
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
     * Display list of staff
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get filters from request
        $filters = [
            'search' => $request->input('search'),
            'user_type' => 'staff',
            'status' => $request->input('status'),
        ];

        // Use UserRepository to get filtered staff
        $staff = $this->userRepository->filterUsers($filters, 15);

        // Log activity
        $this->logActivity('viewed_staff');

        return view('staff.index', [
            'staff' => $staff,
        ]);
    }

    /**
     * Show create staff form
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('staff.create');
    }

    /**
     * Store new staff
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Get validation rules
        $rules = $this->getValidationRules('staff');
        
        // Validate input
        $validated = $request->validate($rules, $this->getValidationMessages());

        try {
            // Use UserRepository to create staff
            $user = $this->userRepository->createStaff($validated);

            // Log activity
            $this->logActivity('created_staff', ['user_id' => $user->id]);

            return redirect()
                ->route('staff.show', $user)
                ->with('success', 'Staff created successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error creating staff: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display staff details
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Get staff with relations
        $staff = $this->userRepository->getUserWithRelations($id);

        if (!$staff || $staff->user_type !== 'staff') {
            abort(404, 'Staff not found');
        }

        // Log activity
        $this->logActivity('viewed_staff_member', ['staff_id' => $staff->id]);

        return view('staff.show', [
            'staff' => $staff,
        ]);
    }

    /**
     * Show edit staff form
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $staff = $this->userRepository->find($id);

        if (!$staff || $staff->user_type !== 'staff') {
            abort(404, 'Staff not found');
        }

        return view('staff.edit', [
            'staff' => $staff,
        ]);
    }

    /**
     * Update staff
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $staff = $this->userRepository->find($id);

        if (!$staff || $staff->user_type !== 'staff') {
            abort(404, 'Staff not found');
        }

        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($staff->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', \Illuminate\Validation\Rule::in('active', 'inactive', 'suspended')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'employee_code_staff' => ['nullable', 'string', 'max:255'],
            'department_staff' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['nullable', \Illuminate\Validation\Rule::in('full-time', 'part-time', 'contract')],
            'hire_date_staff' => ['nullable', 'date'],
        ], $this->getValidationMessages());

        try {
            // Use UserRepository to update staff
            $this->userRepository->update($id, $validated);

            // Log activity
            $this->logActivity('updated_staff', ['staff_id' => $id]);

            return redirect()
                ->route('staff.show', $staff)
                ->with('success', 'Staff updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error updating staff: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete staff
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $staff = $this->userRepository->find($id);

        if (!$staff || $staff->user_type !== 'staff') {
            abort(404, 'Staff not found');
        }

        try {
            $name = $staff->name;

            // Use UserRepository to delete staff
            $this->userRepository->delete($id);

            // Log activity
            $this->logActivity('deleted_staff', ['staff_name' => $name]);

            return redirect()
                ->route('staff.index')
                ->with('success', "Staff '{$name}' deleted successfully!");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error deleting staff: ' . $e->getMessage());
        }
    }

    /**
     * Get staff statistics (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $stats = $this->userRepository->getStaffStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get staff by position (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByPosition(Request $request)
    {
        $position = $request->input('position');

        if (!$position) {
            return response()->json(['error' => 'Position is required'], 400);
        }

        // Use UserRepository
        $staff = $this->userRepository->getUsersByType('staff', 15);

        return response()->json([
            'success' => true,
            'data' => $staff,
        ]);
    }

    /**
     * Export staff data (API)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $filters = [
            'user_type' => 'staff',
            'status' => $request->input('status'),
        ];

        // Use UserRepository to get data for export
        $staff = $this->userRepository->getForExport($filters);

        // Log activity
        $this->logActivity('exported_staff', ['count' => $staff->count()]);

        return response()->json([
            'export_date' => now()->format('Y-m-d H:i:s'),
            'total_staff' => $staff->count(),
            'staff' => $staff->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                    'status' => ucfirst($member->status),
                    'created_at' => $member->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    /**
     * Get validation rules for staff
     *
     * @param string $userType
     * @return array
     */
    private function getValidationRules($userType)
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'user_type' => ['required', \Illuminate\Validation\Rule::in('staff')],
            'employee_code_staff' => ['required', 'string', 'unique:staffs'],
            'department_staff' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'employment_type' => ['nullable', \Illuminate\Validation\Rule::in('full-time', 'part-time', 'contract')],
            'hire_date_staff' => ['required', 'date'],
        ];
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
            'email.required' => 'The email field is required.',
            'email.unique' => 'The email address already exists.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'employee_code_staff.required' => 'The employee code is required.',
            'employee_code_staff.unique' => 'The employee code already exists.',
            'department_staff.required' => 'The department field is required.',
            'position.required' => 'The position field is required.',
            'hire_date_staff.required' => 'The hire date is required.',
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
                'model' => 'Staff',
                'changes' => !empty($data) ? $data : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail
        }
    }
}
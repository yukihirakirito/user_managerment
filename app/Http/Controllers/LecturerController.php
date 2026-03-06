<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LecturerController extends Controller
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
     * Display list of lecturers
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'user_type' => 'lecturer',
            'status' => $request->input('status'),
        ];

        $lecturers = $this->userRepository->filterUsers($filters, 15);

        $this->logActivity('viewed_lecturers');

        return view('lecturers.index', [
            'lecturers' => $lecturers,
        ]);
    }

    /**
     * Show create lecturer form
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('lecturers.create');
    }

    /**
     * Store new lecturer
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $rules = $this->getValidationRules('lecturer');
        
        $validated = $request->validate($rules, $this->getValidationMessages());

        try {
            $user = $this->userRepository->createLecturer($validated);

            $this->logActivity('created_lecturer', ['user_id' => $user->id]);

            return redirect()
                ->route('lecturers.show', $user)
                ->with('success', 'Lecturer created successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error creating lecturer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display lecturer details
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $lecturer = $this->userRepository->getUserWithRelations($id);

        if (!$lecturer || $lecturer->user_type !== 'lecturer') {
            abort(404, 'Lecturer not found');
        }

        $this->logActivity('viewed_lecturer', ['lecturer_id' => $lecturer->id]);

        return view('lecturers.show', [
            'lecturer' => $lecturer,
        ]);
    }

    /**
     * Show edit lecturer form
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $lecturer = $this->userRepository->find($id);

        if (!$lecturer || $lecturer->user_type !== 'lecturer') {
            abort(404, 'Lecturer not found');
        }

        return view('lecturers.edit', [
            'lecturer' => $lecturer,
        ]);
    }

    /**
     * Update lecturer
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $lecturer = $this->userRepository->find($id);

        if (!$lecturer || $lecturer->user_type !== 'lecturer') {
            abort(404, 'Lecturer not found');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($lecturer->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', \Illuminate\Validation\Rule::in('active', 'inactive', 'suspended')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'employee_code' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'academic_degree' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
        ], $this->getValidationMessages());

        try {
            $this->userRepository->update($id, $validated);

            $this->logActivity('updated_lecturer', ['lecturer_id' => $id]);

            return redirect()
                ->route('lecturers.show', $lecturer)
                ->with('success', 'Lecturer updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error updating lecturer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete lecturer
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $lecturer = $this->userRepository->find($id);

        if (!$lecturer || $lecturer->user_type !== 'lecturer') {
            abort(404, 'Lecturer not found');
        }

        try {
            $name = $lecturer->name;

            $this->userRepository->delete($id);

            $this->logActivity('deleted_lecturer', ['lecturer_name' => $name]);

            return redirect()
                ->route('lecturers.index')
                ->with('success', "Lecturer '{$name}' deleted successfully!");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error deleting lecturer: ' . $e->getMessage());
        }
    }

    /**
     * Get lecturers statistics (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $stats = $this->userRepository->getLecturerStatistics();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get lecturers by department (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByDepartment(Request $request)
    {
        $department = $request->input('department');

        if (!$department) {
            return response()->json(['error' => 'Department is required'], 400);
        }

        $lecturers = $this->userRepository->getUsersByType('lecturer', 15);

        return response()->json([
            'success' => true,
            'data' => $lecturers,
        ]);
    }

    /**
     * Export lecturers data (API)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $filters = [
            'user_type' => 'lecturer',
            'status' => $request->input('status'),
        ];

        $lecturers = $this->userRepository->getForExport($filters);

        $this->logActivity('exported_lecturers', ['count' => $lecturers->count()]);

        return response()->json([
            'export_date' => now()->format('Y-m-d H:i:s'),
            'total_lecturers' => $lecturers->count(),
            'lecturers' => $lecturers->map(function ($lecturer) {
                return [
                    'id' => $lecturer->id,
                    'name' => $lecturer->name,
                    'email' => $lecturer->email,
                    'phone' => $lecturer->phone,
                    'status' => ucfirst($lecturer->status),
                    'created_at' => $lecturer->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    /**
     * Get validation rules for lecturer
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
            'user_type' => ['required', \Illuminate\Validation\Rule::in('lecturer')],
            'employee_code' => ['required', 'string', 'unique:lecturers'],
            'department' => ['required', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'academic_degree' => ['nullable', 'string', 'max:255'],
            'hire_date_lecturer' => ['required', 'date'],
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
            'employee_code.required' => 'The employee code is required.',
            'employee_code.unique' => 'The employee code already exists.',
            'department.required' => 'The department field is required.',
            'hire_date_lecturer.required' => 'The hire date is required.',
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
                'model' => 'Lecturer',
                'changes' => !empty($data) ? $data : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}
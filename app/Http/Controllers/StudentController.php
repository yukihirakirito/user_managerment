<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class StudentController extends Controller
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
     * Display list of students
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get filters from request
        $filters = [
            'search' => $request->input('search'),
            'user_type' => 'student',
            'status' => $request->input('status'),
        ];

        // Use UserRepository to get filtered students
        $students = $this->userRepository->filterUsers($filters, 15);

        // Log activity
        $this->logActivity('viewed_students');

        return view('students.index', [
            'students' => $students,
        ]);
    }

    /**
     * Show create student form
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store new student
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Get validation rules
        $rules = $this->getValidationRules('student');
        
        // Validate input
        $validated = $request->validate($rules, $this->getValidationMessages());

        try {
            // Use UserRepository to create student
            $user = $this->userRepository->createStudent($validated);

            // Log activity
            $this->logActivity('created_student', ['user_id' => $user->id]);

            return redirect()
                ->route('students.show', $user)
                ->with('success', 'Student created successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error creating student: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display student details
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Get student with relations
        $student = $this->userRepository->getUserWithRelations($id);

        if (!$student || $student->user_type !== 'student') {
            abort(404, 'Student not found');
        }

        // Log activity
        $this->logActivity('viewed_student', ['student_id' => $student->id]);

        return view('students.show', [
            'student' => $student,
        ]);
    }

    /**
     * Show edit student form
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $student = $this->userRepository->find($id);

        if (!$student || $student->user_type !== 'student') {
            abort(404, 'Student not found');
        }

        return view('students.edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update student
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $student = $this->userRepository->find($id);

        if (!$student || $student->user_type !== 'student') {
            abort(404, 'Student not found');
        }

        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($student->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', \Illuminate\Validation\Rule::in('active', 'inactive', 'suspended')],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'student_code' => ['nullable', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'enrollment_date' => ['nullable', 'date'],
            'graduation_date' => ['nullable', 'date'],
            'gpa' => ['nullable', 'numeric', 'between:0,4'],
        ], $this->getValidationMessages());

        try {
            // Use UserRepository to update student
            $this->userRepository->update($id, $validated);

            // Log activity
            $this->logActivity('updated_student', ['student_id' => $id]);

            return redirect()
                ->route('students.show', $student)
                ->with('success', 'Student updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error updating student: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete student
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $student = $this->userRepository->find($id);

        if (!$student || $student->user_type !== 'student') {
            abort(404, 'Student not found');
        }

        try {
            $name = $student->name;

            // Use UserRepository to delete student
            $this->userRepository->delete($id);

            // Log activity
            $this->logActivity('deleted_student', ['student_name' => $name]);

            return redirect()
                ->route('students.index')
                ->with('success', "Student '{$name}' deleted successfully!");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    /**
     * Get students statistics (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $stats = $this->userRepository->getStudentStatistics();
    
        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get students by major (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByMajor(Request $request)
    {
        $major = $request->input('major');

        if (!$major) {
            return response()->json(['error' => 'Major is required'], 400);
        }

        // Use UserRepository
        $students = $this->userRepository->getUsersByType('student', 15);

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    /**
     * Export students data (API)
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $filters = [
            'user_type' => 'student',
            'status' => $request->input('status'),
        ];

        // Use UserRepository to get data for export
        $students = $this->userRepository->getForExport($filters);

        // Log activity
        $this->logActivity('exported_students', ['count' => $students->count()]);

        return response()->json([
            'export_date' => now()->format('Y-m-d H:i:s'),
            'total_students' => $students->count(),
            'students' => $students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'status' => ucfirst($student->status),
                    'created_at' => $student->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ]);
    }

    /**
     * Get validation rules for student
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
            'user_type' => ['required', \Illuminate\Validation\Rule::in('student')],
            'student_code' => ['required', 'string', 'unique:students'],
            'major' => ['required', 'string', 'max:255'],
            'enrollment_date' => ['required', 'date'],
            'graduation_date' => ['nullable', 'date'],
            'gpa' => ['nullable', 'numeric', 'between:0,4'],
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
            'student_code.required' => 'The student code is required.',
            'student_code.unique' => 'The student code already exists.',
            'major.required' => 'The major field is required.',
            'enrollment_date.required' => 'The enrollment date is required.',
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
                'model' => 'Student',
                'changes' => !empty($data) ? $data : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\UserController;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display list of students
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'student');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Major filter
        if ($request->filled('major')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('major', $request->input('major'));
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $students = $query->paginate(15);

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
        // Delegate to UserController
        $userController = new UserController();
        return $userController->store($request);
    }

    /**
     * Display student details
     *
     * @param \App\Models\User $student
     * @return \Illuminate\View\View
     */
    public function show(User $student)
    {
        if ($student->user_type !== 'student') {
            abort(404);
        }

        return view('students.show', [
            'student' => $student,
        ]);
    }

    /**
     * Show edit student form
     *
     * @param \App\Models\User $student
     * @return \Illuminate\View\View
     */
    public function edit(User $student)
    {
        if ($student->user_type !== 'student') {
            abort(404);
        }

        return view('students.edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update student
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $student)
    {
        if ($student->user_type !== 'student') {
            abort(404);
        }

        $userController = new UserController();
        return $userController->update($request, $student);
    }

    /**
     * Delete student
     *
     * @param \App\Models\User $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $student)
    {
        if ($student->user_type !== 'student') {
            abort(404);
        }

        $userController = new UserController();
        return $userController->destroy($student);
    }

    /**
     * Get students statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        return response()->json([
            'total_students' => User::where('user_type', 'student')->count(),
            'active_students' => User::where('user_type', 'student')
                ->where('status', 'active')
                ->count(),
            'average_gpa' => Student::avg('gpa'),
            'majors' => Student::distinct('major')->pluck('major'),
        ]);
    }

    /**
     * Get students by major
     *
     * @param string $major
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByMajor($major)
    {
        $students = User::where('user_type', 'student')
            ->whereHas('student', function ($query) use ($major) {
                $query->where('major', $major);
            })
            ->get();

        return response()->json($students);
    }

    /**
     * Export students data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function export()
    {
        $students = User::where('user_type', 'student')->with('student')->get();

        return response()->json([
            'export_date' => now()->format('Y-m-d H:i:s'),
            'total_students' => $students->count(),
            'students' => $students,
        ]);
    }
}
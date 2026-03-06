<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\UserController;
use App\Models\User;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    /**
     * Display list of lecturers
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'lecturer');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Department filter
        if ($request->filled('department')) {
            $query->whereHas('lecturer', function ($q) use ($request) {
                $q->where('department', $request->input('department'));
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $lecturers = $query->paginate(15);

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
        $userController = new UserController();
        return $userController->store($request);
    }

    /**
     * Display lecturer details
     *
     * @param \App\Models\User $lecturer
     * @return \Illuminate\View\View
     */
    public function show(User $lecturer)
    {
        if ($lecturer->user_type !== 'lecturer') {
            abort(404);
        }

        return view('lecturers.show', [
            'lecturer' => $lecturer,
        ]);
    }

    /**
     * Show edit lecturer form
     *
     * @param \App\Models\User $lecturer
     * @return \Illuminate\View\View
     */
    public function edit(User $lecturer)
    {
        if ($lecturer->user_type !== 'lecturer') {
            abort(404);
        }

        return view('lecturers.edit', [
            'lecturer' => $lecturer,
        ]);
    }

    /**
     * Update lecturer
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $lecturer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $lecturer)
    {
        if ($lecturer->user_type !== 'lecturer') {
            abort(404);
        }

        $userController = new UserController();
        return $userController->update($request, $lecturer);
    }

    /**
     * Delete lecturer
     *
     * @param \App\Models\User $lecturer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $lecturer)
    {
        if ($lecturer->user_type !== 'lecturer') {
            abort(404);
        }

        $userController = new UserController();
        return $userController->destroy($lecturer);
    }

    /**
     * Get lecturers statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        return response()->json([
            'total_lecturers' => User::where('user_type', 'lecturer')->count(),
            'active_lecturers' => User::where('user_type', 'lecturer')
                ->where('status', 'active')
                ->count(),
            'departments' => Lecturer::distinct('department')->pluck('department'),
            'academic_degrees' => Lecturer::distinct('academic_degree')->pluck('academic_degree'),
        ]);
    }

    /**
     * Get lecturers by department
     *
     * @param string $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByDepartment($department)
    {
        $lecturers = User::where('user_type', 'lecturer')
            ->whereHas('lecturer', function ($query) use ($department) {
                $query->where('department', $department);
            })
            ->get();

        return response()->json($lecturers);
    }

    /**
     * Get lecturers by degree
     *
     * @param string $degree
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByDegree($degree)
    {
        $lecturers = User::where('user_type', 'lecturer')
            ->whereHas('lecturer', function ($query) use ($degree) {
                $query->where('academic_degree', $degree);
            })
            ->get();

        return response()->json($lecturers);
    }

    /**
     * Export lecturers data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function export()
    {
        $lecturers = User::where('user_type', 'lecturer')->with('lecturer')->get();

        return response()->json([
            'export_date' => now()->format('Y-m-d H:i:s'),
            'total_lecturers' => $lecturers->count(),
            'lecturers' => $lecturers,
        ]);
    }
}
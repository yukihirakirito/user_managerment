<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\UserController;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display list of staff members
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::where('user_type', 'staff');

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
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('department', $request->input('department'));
            });
        }

        // Employment type filter
        if ($request->filled('employment_type')) {
            $query->whereHas('staff', function ($q) use ($request) {
                $q->where('employment_type', $request->input('employment_type'));
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $staff = $query->paginate(15);

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
     * Store new staff member
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
     * Display staff details
     *
     * @param \App\Models\User $staff
     * @return \Illuminate\View\View
     */
    public function show(User $staff)
    {
        if ($staff->user_type !== 'staff') {
            abort(404);
        }

        return view('staff.show', [
            'staff' => $staff,
        ]);
    }

    /**
     * Show edit staff form
     *
     * @param \App\Models\User $staff
     * @return \Illuminate\View\View
     */
    public function edit(User $staff)
    {
        if ($staff->user_type !== 'staff') {
            abort(404);
        }

        return view('staff.edit', [
            'staff' => $staff,
        ]);
    }

    /**
     * Update staff member
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $staff
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $staff)
    {
        if ($staff->user_type !== 'staff') {
            abort(404);
        }

        $userController = new UserController();
        return $userController->update($request, $staff);
    }

    /**
     * Delete staff member
     *
     * @param \App\Models\User $staff
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $staff)
    {
        if ($staff->user_type !== 'staff') {
            abort(404);
        }

        $userController = new UserController();
        return $userController->destroy($staff);
    }

    /**
     * Get staff statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        return response()->json([
            'total_staff' => User::where('user_type', 'staff')->count(),
            'active_staff' => User::where('user_type', 'staff')
                ->where('status', 'active')
                ->count(),
            'departments' => Staff::distinct('department')->pluck('department'),
            'positions' => Staff::distinct('position')->pluck('position'),
            'employment_types' => Staff::distinct('employment_type')->pluck('employment_type'),
        ]);
    }

    /**
     * Get staff by department
     *
     * @param string $department
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByDepartment($department)
    {
        $staff = User::where('user_type', 'staff')
            ->whereHas('staff', function ($query) use ($department) {
                $query->where('department', $department);
            })
            ->get();

        return response()->json($staff);
    }

    /**
     * Get staff by position
     *
     * @param string $position
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByPosition($position)
    {
        $staff = User::where('user_type', 'staff')
            ->whereHas('staff', function ($query) use ($position) {
                $query->where('position', $position);
            })
            ->get();

        return response()->json($staff);
    }

    /**
     * Get staff by employment type
     *
     * @param string $employmentType
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByEmploymentType($employmentType)
    {
        $staff = User::where('user_type', 'staff')
            ->whereHas('staff', function ($query) use ($employmentType) {
                $query->where('employment_type', $employmentType);
            })
            ->get();

        return response()->json($staff);
    }

    /**
     * Export staff data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function export()
    {
        $staff = User::where('user_type', 'staff')->with('staff')->get();

        return response()->json([
            'export_date' => now()->format('Y-m-d H:i:s'),
            'total_staff' => $staff->count(),
            'staff' => $staff,
        ]);
    }
}
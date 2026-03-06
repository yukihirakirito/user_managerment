<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get statistics
        $totalUsers = User::count();
        $totalStudents = User::where('user_type', 'student')->count();
        $totalLecturers = User::where('user_type', 'lecturer')->count();
        $totalStaff = User::where('user_type', 'staff')->count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();
        $suspendedUsers = User::where('status', 'suspended')->count();

        // Get recent users (last 10)
        $recentUsers = User::latest()
            ->take(10)
            ->get();

        // Get statistics by type
        $usersByType = [
            'student' => $totalStudents,
            'lecturer' => $totalLecturers,
            'staff' => $totalStaff,
        ];

        // Get statistics by status
        $usersByStatus = [
            'active' => $activeUsers,
            'inactive' => $inactiveUsers,
            'suspended' => $suspendedUsers,
        ];

        // Pass data to view
        return view('dashboard.index', [
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalLecturers' => $totalLecturers,
            'totalStaff' => $totalStaff,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'suspendedUsers' => $suspendedUsers,
            'recentUsers' => $recentUsers,
            'usersByType' => $usersByType,
            'usersByStatus' => $usersByStatus,
        ]);
    }

    /**
     * Get dashboard statistics (for API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        return response()->json([
            'totalUsers' => User::count(),
            'totalStudents' => User::where('user_type', 'student')->count(),
            'totalLecturers' => User::where('user_type', 'lecturer')->count(),
            'totalStaff' => User::where('user_type', 'staff')->count(),
            'activeUsers' => User::where('status', 'active')->count(),
            'inactiveUsers' => User::where('status', 'inactive')->count(),
            'suspendedUsers' => User::where('status', 'suspended')->count(),
        ]);
    }

    /**
     * Get recent users data (for API)
     *
     * @param int $limit
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentUsers($limit = 10)
    {
        $users = User::latest()
            ->take($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'type' => ucfirst($user->user_type),
                    'status' => ucfirst($user->status),
                    'created_at' => $user->created_at->format('M d, Y'),
                    'created_at_human' => $user->created_at->diffForHumans(),
                ];
            });

        return response()->json($users);
    }

    /**
     * Get user type distribution (for API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserDistribution()
    {
        $distribution = [
            'students' => User::where('user_type', 'student')->count(),
            'lecturers' => User::where('user_type', 'lecturer')->count(),
            'staff' => User::where('user_type', 'staff')->count(),
        ];

        return response()->json($distribution);
    }

    /**
     * Get user status distribution (for API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatusDistribution()
    {
        $distribution = [
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
        ];

        return response()->json($distribution);
    }

    /**
     * Get daily user registration statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyStatistics()
    {
        $daily = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            });

        return response()->json($daily);
    }

    /**
     * Get user type statistics by status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserTypeByStatus()
    {
        $data = [];

        foreach (['student', 'lecturer', 'staff'] as $type) {
            $data[$type] = [
                'active' => User::where('user_type', $type)
                    ->where('status', 'active')
                    ->count(),
                'inactive' => User::where('user_type', $type)
                    ->where('status', 'inactive')
                    ->count(),
                'suspended' => User::where('user_type', $type)
                    ->where('status', 'suspended')
                    ->count(),
            ];
        }

        return response()->json($data);
    }

    /**
     * Export dashboard data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function exportData()
    {
        $data = [
            'export_date' => now()->format('Y-m-d H:i:s'),
            'summary' => [
                'totalUsers' => User::count(),
                'totalStudents' => User::where('user_type', 'student')->count(),
                'totalLecturers' => User::where('user_type', 'lecturer')->count(),
                'totalStaff' => User::where('user_type', 'staff')->count(),
                'activeUsers' => User::where('status', 'active')->count(),
                'inactiveUsers' => User::where('status', 'inactive')->count(),
                'suspendedUsers' => User::where('status', 'suspended')->count(),
            ],
            'users' => User::all(),
        ];

        return response()->json($data);
    }
}
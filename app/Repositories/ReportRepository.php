<?php

namespace App\Repositories;

use App\Models\ActivityLog;
use App\Repositories\Contracts\ReportRepositoryInterface;

class ReportRepository extends Repository implements ReportRepositoryInterface
{
    /**
     * Get model instance
     *
     * @return ActivityLog
     */
    public function getModel()
    {
        return new ActivityLog();
    }

    /**
     * Get analytics data
     *
     * @return array
     */
    public function getAnalytics()
    {
        return [
            'total_users' => \App\Models\User::count(),
            'total_students' => \App\Models\User::where('user_type', 'student')->count(),
            'total_lecturers' => \App\Models\User::where('user_type', 'lecturer')->count(),
            'total_staff' => \App\Models\User::where('user_type', 'staff')->count(),
            'active_users' => \App\Models\User::where('status', 'active')->count(),
            'inactive_users' => \App\Models\User::where('status', 'inactive')->count(),
            'suspended_users' => \App\Models\User::where('status', 'suspended')->count(),
            'total_activities' => $this->model->count(),
            'today_activities' => $this->model->whereDate('created_at', today())->count(),
            'week_activities' => $this->model->whereBetween('created_at', [
                now()->subDays(7),
                now()
            ])->count(),
        ];
    }

    /**
     * Get activity logs with pagination
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getActivityLogs($perPage = 20)
    {
        return $this->model
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get activity logs by user
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getActivityLogsByUser($userId, $perPage = 20)
    {
        return $this->model
            ->where('user_id', $userId)
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get activity logs by action
     *
     * @param string $action
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getActivityLogsByAction($action, $perPage = 20)
    {
        return $this->model
            ->where('action', $action)
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get activity logs by date range
     *
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getActivityLogsByDateRange($startDate, $endDate, $perPage = 20)
    {
        return $this->model
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get activity summary by action
     *
     * @return array
     */
    public function getActivitySummary()
    {
        return [
            'created' => $this->model->where('action', 'created')->count(),
            'updated' => $this->model->where('action', 'updated')->count(),
            'deleted' => $this->model->where('action', 'deleted')->count(),
            'login' => $this->model->where('action', 'login')->count(),
            'logout' => $this->model->where('action', 'logout')->count(),
        ];
    }

    /**
     * Get top active users
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopActiveUsers($limit = 10)
    {
        return $this->model
            ->select('user_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as activity_count'))
            ->groupBy('user_id')
            ->orderByDesc('activity_count')
            ->with('user')
            ->take($limit)
            ->get();
    }

    /**
     * Get recent activities
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActivities($limit = 10)
    {
        return $this->model
            ->with('user')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get activity logs for export
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForExport(array $filters = [])
    {
        $query = $this->model;

        if (!empty($filters['user_id'])) {
            $query = $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $query = $query->where('action', $filters['action']);
        }

        if (!empty($filters['from_date'])) {
            $query = $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query = $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        return $query->with('user')->get();
    }

    /**
     * Get user statistics
     *
     * @return array
     */
    public function getUserStatistics()
    {
        return [
            'by_type' => \App\Models\User::selectRaw('user_type, COUNT(*) as count')
                ->groupBy('user_type')
                ->get()
                ->pluck('count', 'user_type')
                ->toArray(),
            'by_status' => \App\Models\User::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray(),
        ];
    }

    /**
     * Get daily activities (last 30 days)
     *
     * @return array
     */
    public function getDailyActivities()
    {
        $activities = $this->model
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $activities->map(function ($activity) {
            return [
                'date' => $activity->date,
                'count' => $activity->count,
            ];
        })->toArray();
    }
}
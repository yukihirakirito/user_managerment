<?php

namespace App\Http\Controllers;

use App\Repositories\ReportRepository;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * @var ReportRepository
     */
    protected $reportRepository;

    /**
     * Constructor
     *
     * @param ReportRepository $reportRepository
     */
    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Display analytics page
     *
     * @return \Illuminate\View\View
     */
    public function analytics()
    {
        $analytics = $this->reportRepository->getAnalytics();
        $activitySummary = $this->reportRepository->getActivitySummary();
        $topActiveUsers = $this->reportRepository->getTopActiveUsers(10);
        $dailyActivities = $this->reportRepository->getDailyActivities();
        $userStatistics = $this->reportRepository->getUserStatistics();

        return view('reports.analytics', [
            'analytics' => $analytics,
            'activitySummary' => $activitySummary,
            'topActiveUsers' => $topActiveUsers,
            'dailyActivities' => $dailyActivities,
            'userStatistics' => $userStatistics,
        ]);
    }

    /**
     * Display activity logs page
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function activity(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        
        // Get logs based on filters
        if ($request->filled('user_id')) {
            $logs = $this->reportRepository->getActivityLogsByUser(
                $request->input('user_id'),
                $perPage
            );
        } elseif ($request->filled('action')) {
            $logs = $this->reportRepository->getActivityLogsByAction(
                $request->input('action'),
                $perPage
            );
        } else {
            $logs = $this->reportRepository->getActivityLogs($perPage);
        }

        $activitySummary = $this->reportRepository->getActivitySummary();

        return view('reports.activity', [
            'logs' => $logs,
            'activitySummary' => $activitySummary,
        ]);
    }

    /**
     * Get analytics data (API)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAnalyticsData()
    {
        $data = $this->reportRepository->getAnalytics();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get activity logs (API)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivityData(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $logs = $this->reportRepository->getActivityLogs($perPage);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    /**
     * Get daily activities chart data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDailyActivityChart()
    {
        $data = $this->reportRepository->getDailyActivities();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
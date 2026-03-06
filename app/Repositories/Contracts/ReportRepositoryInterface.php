<?php

namespace App\Repositories\Contracts;

interface ReportRepositoryInterface
{
    /**
     * Get analytics data
     *
     * @return array
     */
    public function getAnalytics();

    /**
     * Get activity logs with pagination
     *
     * @param int $perPage
     * @return mixed
     */
    public function getActivityLogs($perPage = 20);

    /**
     * Get activity logs by user
     *
     * @param int $userId
     * @param int $perPage
     * @return mixed
     */
    public function getActivityLogsByUser($userId, $perPage = 20);

    /**
     * Get activity logs by action
     *
     * @param string $action
     * @param int $perPage
     * @return mixed
     */
    public function getActivityLogsByAction($action, $perPage = 20);

    /**
     * Get activity summary
     *
     * @return array
     */
    public function getActivitySummary();

    /**
     * Get top active users
     *
     * @param int $limit
     * @return mixed
     */
    public function getTopActiveUsers($limit = 10);

    /**
     * Get recent activities
     *
     * @param int $limit
     * @return mixed
     */
    public function getRecentActivities($limit = 10);

    /**
     * Get activity logs for export
     *
     * @param array $filters
     * @return mixed
     */
    public function getForExport(array $filters = []);

    /**
     * Get user statistics
     *
     * @return array
     */
    public function getUserStatistics();

    /**
     * Get daily activities
     *
     * @return array
     */
    public function getDailyActivities();
}
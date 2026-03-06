<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'activity_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'changes',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'changes' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the activity.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get formatted action text
     *
     * @return string
     */
    public function getActionTextAttribute()
    {
        $actions = [
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'login' => 'Logged in',
            'logout' => 'Logged out',
            'status_changed' => 'Status changed',
        ];

        return isset($actions[$this->action]) ? $actions[$this->action] : ucfirst($this->action);
    }

    /**
     * Get action icon
     *
     * @return string
     */
    public function getActionIconAttribute()
    {
        $icons = [
            'created' => 'bi-plus-circle',
            'updated' => 'bi-pencil-square',
            'deleted' => 'bi-trash',
            'login' => 'bi-box-arrow-in-right',
            'logout' => 'bi-box-arrow-right',
            'status_changed' => 'bi-exclamation-circle',
        ];

        return isset($icons[$this->action]) ? $icons[$this->action] : 'bi-info-circle';
    }

    /**
     * Get action badge color
     *
     * @return string
     */
    public function getActionColorAttribute()
    {
        $colors = [
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'login' => 'primary',
            'logout' => 'secondary',
            'status_changed' => 'warning',
        ];

        return isset($colors[$this->action]) ? $colors[$this->action] : 'secondary';
    }

    /**
     * Scope: Filter by user
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Filter by action
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $action
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Filter by model
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByModel($query, $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Scope: Filter by date range
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $startDate
     * @param $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope: Get today's activities
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', \Carbon\Carbon::today());
    }

    /**
     * Scope: Get recent activities
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', \Carbon\Carbon::now()->subDays($days));
    }
}
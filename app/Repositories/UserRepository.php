<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Staff;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * Get model instance
     *
     * @return User
     */
    public function getModel()
    {
        return new User();
    }

    /**
     * Get all users with pagination
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getAllUsers($perPage = 15)
    {
        return $this->model
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Get users by type (student, lecturer, staff)
     *
     * @param string $userType
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getUsersByType($userType, $perPage = 15)
    {
        return $this->model
            ->where('user_type', $userType)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get users by status
     *
     * @param string $status
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function getUsersByStatus($status, $perPage = 15)
    {
        return $this->model
            ->where('status', $status)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Search users by name, email, or phone
     *
     * @param string $query
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function searchUsers($query, $perPage = 15)
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Advanced filter users
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function filterUsers(array $filters, $perPage = 15)
    {
        $query = $this->model;

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query = $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // User type filter
        if (!empty($filters['user_type'])) {
            $query = $query->where('user_type', $filters['user_type']);
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query = $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        
        return $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'user_type' => $data['user_type'],
            'status' => $data['status'] ?? 'active',
        ]);
    }

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return User
     */
    public function update($id, array $data)
    {
        $user = $this->find($id);

        if (!$user) {
            return null;
        }

        $updateData = [
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'phone' => $data['phone'] ?? $user->phone,
            'status' => $data['status'] ?? $user->status,
        ];

        // Update password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return $user;
    }

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $user = $this->find($id);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    /**
     * Create student user
     *
     * @param array $data
     * @return User
     */
    public function createStudent(array $data)
    {
        // Create user
        $user = $this->create($data);

        // Create student record
        if ($user) {
            Student::create([
                'user_id' => $user->id,
                'student_code' => $data['student_code'],
                'major' => $data['major'],
                'enrollment_date' => $data['enrollment_date'],
                'graduation_date' => $data['graduation_date'] ?? null,
                'gpa' => $data['gpa'] ?? null,
            ]);
        }

        return $user;
    }

    /**
     * Create lecturer user
     *
     * @param array $data
     * @return User
     */
    public function createLecturer(array $data)
    {
        // Create user
        $user = $this->create($data);

        // Create lecturer record
        if ($user) {
            Lecturer::create([
                'user_id' => $user->id,
                'employee_code' => $data['employee_code'],
                'department' => $data['department'],
                'specialization' => $data['specialization'] ?? null,
                'academic_degree' => $data['academic_degree'] ?? null,
                'hire_date' => $data['hire_date_lecturer'] ?? null,
            ]);
        }

        return $user;
    }

    /**
     * Create staff user
     *
     * @param array $data
     * @return User
     */
    public function createStaff(array $data)
    {
        // Create user
        $user = $this->create($data);

        // Create staff record
        if ($user) {
            Staff::create([
                'user_id' => $user->id,
                'employee_code' => $data['employee_code_staff'],
                'department' => $data['department_staff'],
                'position' => $data['position'],
                'employment_type' => $data['employment_type'] ?? 'full-time',
                'hire_date' => $data['hire_date_staff'] ?? null,
            ]);
        }

        return $user;
    }

    /**
     * Get user with related models
     *
     * @param int $id
     * @return User|null
     */
    public function getUserWithRelations($id)
    {
        return $this->model
            ->with(['student', 'lecturer', 'staff'])
            ->find($id);
    }

    /**
     * Count users by type
     *
     * @return array
     */
    public function countByType()
    {
        return [
            'total' => $this->model->count(),
            'students' => $this->model->where('user_type', 'student')->count(),
            'lecturers' => $this->model->where('user_type', 'lecturer')->count(),
            'staff' => $this->model->where('user_type', 'staff')->count(),
        ];
    }

    /**
     * Count users by status
     *
     * @return array
     */
    public function countByStatus()
    {
        return [
            'active' => $this->model->where('status', 'active')->count(),
            'inactive' => $this->model->where('status', 'inactive')->count(),
            'suspended' => $this->model->where('status', 'suspended')->count(),
        ];
    }

    /**
     * Update user status
     *
     * @param int $id
     * @param string $status
     * @return User|null
     */
    public function updateStatus($id, $status)
    {
        $user = $this->find($id);

        if (!$user) {
            return null;
        }

        $user->update(['status' => $status]);

        return $user;
    }

    /**
     * Get recent users
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecent($limit = 10)
    {
        return $this->model
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get users for export
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getForExport(array $filters = [])
    {
        $query = $this->model;

        if (!empty($filters['user_type'])) {
            $query = $query->where('user_type', $filters['user_type']);
        }

        if (!empty($filters['status'])) {
            $query = $query->where('status', $filters['status']);
        }

        return $query->get();
    }

    /**
     * Check if email exists
     *
     * @param string $email
     * @param int|null $exceptId
     * @return bool
     */
    public function emailExists($email, $exceptId = null)
    {
        $query = $this->model->where('email', $email);

        if ($exceptId) {
            $query = $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    /**
     * Get user statistics
     *
     * @return array
     */
    public function getStatistics()
    {
        return [
            'total_users' => $this->model->count(),
            'by_type' => $this->countByType(),
            'by_status' => $this->countByStatus(),
            'recent' => $this->getRecent(5),
        ];
    }

    /**
     * Count students statistics
     *
     * @return array
     */
    public function getStudentStatistics()
    {
        return [
            'total_students' => $this->model->where('user_type', 'student')->count(),
            'active_students' => $this->model->where('user_type', 'student')
                ->where('status', 'active')->count(),
            'inactive_students' => $this->model->where('user_type', 'student')
                ->where('status', 'inactive')->count(),
            'suspended_students' => $this->model->where('user_type', 'student')
                ->where('status', 'suspended')->count(),
        ];
    }

    /**
     * Count lecturers statistics
     *
     * @return array
     */
    public function getLecturerStatistics()
    {
        return [
            'total_lecturers' => $this->model->where('user_type', 'lecturer')->count(),
            'active_lecturers' => $this->model->where('user_type', 'lecturer')
                ->where('status', 'active')->count(),
            'inactive_lecturers' => $this->model->where('user_type', 'lecturer')
                ->where('status', 'inactive')->count(),
            'suspended_lecturers' => $this->model->where('user_type', 'lecturer')
                ->where('status', 'suspended')->count(),
        ];
    }

    /**
     * Count staff statistics
     *
     * @return array
     */
    public function getStaffStatistics()
    {
        return [
            'total_staff' => $this->model->where('user_type', 'staff')->count(),
            'active_staff' => $this->model->where('user_type', 'staff')
                ->where('status', 'active')->count(),
            'inactive_staff' => $this->model->where('user_type', 'staff')
                ->where('status', 'inactive')->count(),
            'suspended_staff' => $this->model->where('user_type', 'staff')
                ->where('status', 'suspended')->count(),
        ];
    }
}
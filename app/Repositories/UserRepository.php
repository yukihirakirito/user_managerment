<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository extends Repository implements UserRepositoryInterface
{
    public function getModel()
    {
        return new User();
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getUsersByType($userType, $perPage = 15)
    {
        return $this->model
            ->where('user_type', $userType)
            ->paginate($perPage);
    }

    public function searchUsers($query, $perPage = 15)
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    public function createStudent(array $data)
    {
        // Implementation
    }

    public function createLecturer(array $data)
    {
        // Implementation
    }

    public function createStaff(array $data)
    {
        // Implementation
    }

    public function getUserWithRelations($id)
    {
        return $this->model
            ->with(['student', 'lecturer', 'staff'])
            ->find($id);
    }
}
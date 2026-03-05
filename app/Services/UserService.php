<?php
namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers($page = 1, $perPage = 15)
    {
        return $this->userRepository->paginate($perPage);
    }

    public function createStudent(array $data)
    {
        return $this->userRepository->createStudent($data);
    }
}
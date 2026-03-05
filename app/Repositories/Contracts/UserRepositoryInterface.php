<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function findByEmail($email);
    public function getUsersByType($userType, $perPage = 15);
    public function searchUsers($query, $perPage = 15);
    public function createStudent(array $data);
    public function createLecturer(array $data);
    public function createStaff(array $data);
    public function getUserWithRelations($id);
}
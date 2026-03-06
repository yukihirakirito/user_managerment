<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    /**
     * Get all users
     *
     * @param int $perPage
     * @return mixed
     */
    public function getAllUsers($perPage = 15);

    /**
     * Find user by ID
     *
     * @param int $id
     * @return mixed
     */
    public function find($id);

    /**
     * Find user by email
     *
     * @param string $email
     * @return mixed
     */
    public function findByEmail($email);

    /**
     * Create user
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update user
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data);

    /**
     * Delete user
     *
     * @param int $id
     * @return bool
     */
    public function delete($id);

    /**
     * Search users
     *
     * @param string $query
     * @param int $perPage
     * @return mixed
     */
    public function searchUsers($query, $perPage = 15);

    /**
     * Get users by type
     *
     * @param string $userType
     * @param int $perPage
     * @return mixed
     */
    public function getUsersByType($userType, $perPage = 15);

    /**
     * Get user with relations
     *
     * @param int $id
     * @return mixed
     */
    public function getUserWithRelations($id);
}
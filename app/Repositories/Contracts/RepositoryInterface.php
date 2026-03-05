<?php

namespace App\Repositories\Contracts;

interface RepositoryInterface
{
    public function all($columns = ['*']);
    public function paginate($perPage = 15, $columns = ['*']);
    public function find($id, $columns = ['*']);
    public function findBy($column, $value, $columns = ['*']);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function deleteBy($column, $value);
}
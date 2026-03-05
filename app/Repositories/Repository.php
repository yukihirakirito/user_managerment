<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    protected $model;

    abstract public function getModel();

    public function __construct()
    {
        $this->model = $this->getModel();
    }

    public function all($columns = ['*'])
    {
        return $this->model->select($columns)->get();
    }

    public function paginate($perPage = 15, $columns = ['*'])
    {
        return $this->model->select($columns)->paginate($perPage);
    }

    public function find($id, $columns = ['*'])
    {
        return $this->model->find($id);
    }

    public function findBy($column, $value, $columns = ['*'])
    {
        return $this->model->where($column, $value)->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        if ($record) {
            $record->update($data);
            return $record;
        }
        return false;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function deleteBy($column, $value)
    {
        return $this->model->where($column, $value)->delete();
    }
}
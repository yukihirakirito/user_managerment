<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->model = $this->getModel();
    }

    /**
     * Get model instance - Must be implemented by child class
     *
     * @return Model
     */
    abstract public function getModel();

    /**
     * Get all records
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Find record by ID
     *
     * @param int $id
     * @return Model|null
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Find record by field
     *
     * @param string $field
     * @param string $value
     * @return Model|null
     */
    public function findBy($field, $value)
    {
        return $this->model->where($field, $value)->first();
    }

    /**
     * Create new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update record
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        $record = $this->find($id);

        if (!$record) {
            return false;
        }

        return $record->update($data);
    }

    /**
     * Delete record
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $record = $this->find($id);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    /**
     * Count total records
     *
     * @return int
     */
    public function count()
    {
        return $this->model->count();
    }

    /**
     * Check if record exists
     *
     * @param int $id
     * @return bool
     */
    public function exists($id)
    {
        return $this->find($id) !== null;
    }

    /**
     * Get paginated records
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\Paginator
     */
    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Get records with where clause
     *
     * @param array $where
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function where(array $where)
    {
        $query = $this->model;

        foreach ($where as $field => $value) {
            $query = $query->where($field, $value);
        }

        return $query->get();
    }

    /**
     * Get records with limit
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function take($limit)
    {
        return $this->model->take($limit)->get();
    }

    /**
     * Order records
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * Get latest records
     *
     * @param string $column
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function latest($column = 'created_at')
    {
        return $this->model->latest($column)->get();
    }

    /**
     * Get oldest records
     *
     * @param string $column
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function oldest($column = 'created_at')
    {
        return $this->model->oldest($column)->get();
    }

    /**
     * Eager load relations
     *
     * @param string|array $relations
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);

        return $this;
    }

    /**
     * Execute query and get results
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        return $this->model->get();
    }

    /**
     * Reset model instance
     *
     * @return void
     */
    public function reset()
    {
        $this->model = $this->getModel();
    }
}
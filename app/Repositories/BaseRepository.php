<?php

namespace App\Repositories;

abstract class BaseRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->getModel();
    }

    public function find($id, $relations = [])
    {
        return $this->model->with($relations)->find($id);
    }

    public function findOrFail($id, $relations = [])
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    public function get()
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($model, array $data)
    {
        return $model->update($data);
    }

    public function delete($model): bool
    {
        $model->delete();

        return true;
    }
}

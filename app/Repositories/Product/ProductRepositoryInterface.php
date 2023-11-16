<?php

namespace App\Repositories\Product;

interface ProductRepositoryInterface
{
    public function get();

    public function create(array $data);

    public function find($id, $relations = []);

    public function findOrFail($id, $relations = []);

    public function update($model, array $data);

    public function delete($model);
}

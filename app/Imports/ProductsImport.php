<?php

namespace App\Imports;

use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class ProductsImport implements ToModel, WithValidation, WithHeadingRow, SkipsOnFailure
{
    use Importable;

    protected $productRepository;
    protected $command;

    public function __construct(ProductRepositoryInterface $productRepository, $command)
    {
        $this->productRepository = $productRepository;
        $this->command = $command;
    }

    public function model(array $row)
    {
        $data = $this->prepareData($row);
        return $this->productRepository->create($data);
    }

    public function rules(): array
    {
        return [
            'productname' => ['required', 'string'],
            'price' => ['required', 'numeric'],
        ];
    }

    protected function prepareData(array $row): array
    {
        return [
            'name' => $row['productname'],
            'price' => $row['price'],
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->command->warn("Validation failed for row: " . json_encode($failure->row())
                . " See logs for details. "
                . json_encode($failure->errors())
            );
        }

        Log::error(json_encode($failures));
    }
}

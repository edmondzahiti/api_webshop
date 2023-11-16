<?php

namespace App\Imports;

use App\Repositories\Customer\CustomerRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class CustomersImport implements ToModel, WithValidation, WithHeadingRow, SkipsOnFailure
{
    use Importable;

    protected $customerRepository;
    protected $command;

    public function __construct(CustomerRepositoryInterface $customerRepository, $command)
    {
        $this->customerRepository = $customerRepository;
        $this->command = $command;
    }

    public function model(array $row)
    {
        $data = $this->prepareData($row);
        return $this->customerRepository->create($data);
    }

    public function rules(): array
    {
        return [
            'job_title' => ['required', 'string'],
            'email_address' => ['required', 'email', 'unique:customers,email'],
            'firstname_lastname' => ['required', 'string'],
            'registered_since' => ['required'],
            'phone' => ['required', 'string'],
        ];
    }

    protected function prepareData(array $row): array
    {
        return [
            'job_title' => $row['job_title'],
            'email' => $row['email_address'],
            'first_name' => $this->getFirstName($row['firstname_lastname']),
            'last_name' => $this->getLastName($row['firstname_lastname']),
            'registered_since' => $this->convertDate($row['registered_since']),
            'phone' => $row['phone'],
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

    protected function getFirstName($fullName): ?string
    {
        return explode(' ', $fullName)[0] ?? null;
    }

    protected function getLastName($fullName): ?string
    {
        $nameParts = explode(' ', $fullName);
        unset($nameParts[0]);
        return implode(' ', $nameParts) ?? null;
    }

    protected function convertDate($excelDate): string
    {
        return Carbon::createFromFormat('l,F j,Y', $excelDate)->toDateString();
    }
}

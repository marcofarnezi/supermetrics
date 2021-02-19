<?php
namespace App\Services\LoadFormat;

use App\Services\Interfaces\LoadFormatInterface;

class SupermetricsFormat implements LoadFormatInterface
{
    public function get(array $data): array
    {
        return $data['data'] ?? [];
    }
}
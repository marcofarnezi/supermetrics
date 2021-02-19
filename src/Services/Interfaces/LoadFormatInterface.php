<?php
namespace App\Services\Interfaces;

interface LoadFormatInterface
{
    public function get(array $data): array;
}
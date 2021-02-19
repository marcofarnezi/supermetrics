<?php
namespace App\Models\Interfaces;

interface ModelInterface
{
    public function isValid(array $data): bool;
    public function setData(array $data): ModelInterface;
    public function getData(): array;
}
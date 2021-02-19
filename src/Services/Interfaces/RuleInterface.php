<?php
namespace App\Services\Interfaces;

use App\Models\Interfaces\ModelInterface;

interface RuleInterface
{
    public function run(ModelInterface $model): array;
    public function showResult(): array;
}
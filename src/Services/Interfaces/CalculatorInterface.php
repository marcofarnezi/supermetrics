<?php
namespace App\Services\Interfaces;

use App\Models\Interfaces\ModelInterface;

interface CalculatorInterface
{
    public function setRule(RuleInterface $rule): CalculatorInterface;
    public function setModel(ModelInterface $model): CalculatorInterface;
    public function calculate(): array;
}
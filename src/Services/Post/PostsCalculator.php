<?php
namespace App\Services\Post;

use App\Models\Interfaces\ModelInterface;
use App\Models\Post;
use App\Services\Interfaces\CalculatorInterface;
use App\Services\Interfaces\RuleInterface;

class PostsCalculator implements CalculatorInterface
{
    protected $rules;

    public function __construct()
    {
        $this->rules = [];
    }

    public function setRule(RuleInterface $rule): CalculatorInterface
    {
        $this->rules[] = $rule;
        return $this;
    }

    public function setModel(ModelInterface $model): CalculatorInterface
    {
        if ($model instanceof Post) {
            /**
             * @var RuleInterface $rule
             */
            foreach ($this->rules as $rule) {
                $rule->run($model);
            }
        }
        return $this;
    }

    public function calculate(): array
    {
        $calculated_result = [];
        /**
         * @var RuleInterface $rule
         */
        foreach ($this->rules as $rule) {
            $calculated_result[] = $rule->showResult();
        }

        return $calculated_result;
    }
}
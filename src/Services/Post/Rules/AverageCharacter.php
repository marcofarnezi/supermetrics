<?php
namespace App\Services\Post\Rules;

use App\Models\Interfaces\ModelInterface;
use App\Services\Interfaces\RuleInterface;

class AverageCharacter implements RuleInterface
{
    const RULE_DESCRIPTION = 'Average character length of posts per month';
    protected $proccessed;

    public function __construct()
    {
        $this->proccessed = [];
    }

    protected function isDataValid(array $data): bool
    {
        return isset($data['created_time'])
            && isset($data['message'])
            && $data['created_time'] instanceof \DateTime;
    }

    public function run(ModelInterface $model): array
    {
        $data = $model->getData();

        if ($this->isDataValid($data)) {
            $this->proccessed[$data['created_time']->format('Y-m')][] = strlen($data['message']);
        }
        return $this->proccessed;
    }

    protected function processResult(): array
    {
        $result = [];
        foreach ($this->proccessed as $month => $posts) {
            $total = count($posts);
            $result[$month] = [
                'total_post' => $total,
                'average_length' => array_sum($posts) / $total
            ];
        }
        ksort($result);
        return $result;
    }

    public function showResult(): array
    {
        return [
            'rule' => self::RULE_DESCRIPTION,
            'result' => $this->processResult()
        ];
    }
}
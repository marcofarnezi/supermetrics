<?php
namespace App\Services\Post\Rules;

use App\Models\Interfaces\ModelInterface;
use App\Services\Interfaces\RuleInterface;
use DateTime;

class TotalNumberPerWeek implements RuleInterface
{
    const RULE_DESCRIPTION = 'Total posts split by week number';
    protected $proccessed;

    public function __construct()
    {
        $this->proccessed = [];
    }

    protected function weekOfYear(DateTime $date): string
    {
        $weekOfYear = intval($date->format('W'));
        if ($date->format('n') == "1" && $weekOfYear > 51) {
            $weekOfYear = 0;
        }
        return $date->format('Y') . '-' .($weekOfYear + 1);
    }

    protected function isDataValid(array $data): bool
    {
        return isset($data['created_time'])
            && $data['created_time'] instanceof \DateTime;
    }

    public function run(ModelInterface $model): array
    {
        $data = $model->getData();

        if ($this->isDataValid($data)) {
            $week = $this->weekOfYear($data['created_time']);
            if (empty($this->proccessed[$week])) {
                $this->proccessed[$week]['num_posts'] = 0;
            }
            $this->proccessed[$week]['num_posts']++;
        }
        return $this->proccessed;
    }

    protected function processResult(): array
    {
        $result = $this->proccessed;
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
<?php
namespace App\Services\Post\Rules;

use App\Models\Interfaces\ModelInterface;
use App\Services\Interfaces\RuleInterface;

class AverageNumberPerUser  implements RuleInterface
{
    const FROM_USER = 'from_id';
    const RULE_DESCRIPTION = 'Average number of posts per user per month';

    protected $proccessed;

    public function __construct()
    {
        $this->proccessed = [];
    }

    protected function isDataValid(array $data): bool
    {
        return isset($data['created_time'])
            && isset($data[self::FROM_USER])
            && $data['created_time'] instanceof \DateTime;
    }

    public function run(ModelInterface $model): array
    {
        $data = $model->getData();

        if ($this->isDataValid($data)) {
            $month = $data['created_time']->format('Y-m');
            $group_by = $this->groupBy();
            $this->proccessed[$month][$data[$group_by]][] = $data;
        }
        return $this->proccessed;
    }

    protected function groupBy(): string
    {
        return self::FROM_USER;
    }

    protected function processResult(): array
    {
        $result = [];
        foreach ($this->proccessed as $month => $users) {
            foreach ($users as $user => $posts) {
                $total = count($posts);
                $result[$month][$user] = [
                    'total_post' => $total,
                ];
            }
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
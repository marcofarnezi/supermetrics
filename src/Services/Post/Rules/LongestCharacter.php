<?php
namespace App\Services\Post\Rules;

use App\Models\Interfaces\ModelInterface;
use App\Services\Interfaces\RuleInterface;

class LongestCharacter  implements RuleInterface
{
    const RULE_DESCRIPTION = 'Longest post by character length per month';
    protected $proccessed;

    public function __construct()
    {
        $this->proccessed = [];
    }

    protected function isDataValid(array $data): bool
    {
        return isset($data['created_time'])
            && isset($data['id'])
            && isset($data['message'])
            && $data['created_time'] instanceof \DateTime;
    }

    public function run(ModelInterface $model): array
    {
        $data = $model->getData();

        if ($this->isDataValid($data)) {
            $month = $data['created_time']->format('Y-m');
            if (empty($this->proccessed[$month])) {
                $this->proccessed[$month]['length'] = 0;
            }
            $message_length = strlen($data['message']);
            if ($message_length > $this->proccessed[$month]['length']) {
                $this->proccessed[$month] = [
                    'post' => $data['id'],
                    'length' => $message_length
                ];
            }
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
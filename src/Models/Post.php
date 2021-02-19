<?php
namespace App\Models;

use App\Models\Interfaces\ModelInterface;
use DateTime;

class Post extends Model
{
    protected $id;
    protected $from_name;
    protected $from_id;
    protected $message;
    protected $type;

    /**
     * @var DateTime
     */
    protected $created_time;

    public function setData(array $data): ModelInterface
    {
        if ($this->isValid($data)) {
            $this->id = $data['id'];
            $this->from_name = $data['from_name'];
            $this->from_id = $data['from_id'];
            $this->message = $data['message'];
            $this->type = $data['type'];
            $this->created_time = new DateTime($data['created_time']);
        }
        return $this;
    }

    public function getData(): array
    {
        return [
            'id' => $this->id,
            'from_name' => $this->from_name,
            'from_id' => $this->from_id,
            'message' => $this->message,
            'type' => $this->type,
            'created_time' => $this->created_time
        ];
    }
}
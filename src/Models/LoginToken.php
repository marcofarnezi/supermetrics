<?php
namespace App\Models;

use App\Models\Interfaces\ModelInterface;

class LoginToken  extends Model
{
    protected $client_id;
    protected $email;
    protected $sl_token;

    public function setData(array $data): ModelInterface
    {
        if ($this->isValid($data)) {
            $this->client_id = $data['client_id'];
            $this->email = $data['email'];
            $this->sl_token = $data['sl_token'];
        }
        return $this;
    }

    public function getData(): array
    {
        return [
            'client_id' => $this->client_id,
            'email' => $this->email,
            'sl_token' => $this->sl_token,
        ];
    }
}
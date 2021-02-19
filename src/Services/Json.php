<?php
namespace App\Services;

use App\Services\Interfaces\DisplayInterface;

class Json implements DisplayInterface
{
    public static function display(array $data): void
    {
        echo json_encode($data);
    }
}
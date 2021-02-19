<?php
namespace App\Models;

use App\Models\Interfaces\ModelInterface;

abstract class Model implements ModelInterface
{
    public function isValid(array $data): bool
    {
        $attribute_names = get_object_vars($this);
        foreach ($attribute_names as $attribute_name => $value) {
            if (! isset($data[$attribute_name])) {
                return false;
            }
        }

        return true;
    }
}
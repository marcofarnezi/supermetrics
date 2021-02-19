<?php
namespace Tests\Unit\Models;

use App\Models\Interfaces\ModelInterface;
use App\Models\Model;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    /**
     * @dataProvider isValidDataProvider
     */
    public function testIsValid(Model $model, array $data, bool $output)
    {
        $this->assertEquals($output, $model->isValid($data));
    }

    public function isValidDataProvider(): array
    {
        $class = new class extends Model {
            protected $test;

            public function setData(array $data): ModelInterface
            {
                return $this;
            }

            public function getData(): array
            {
                return [];
            }
        };
        return [
            [$class, ['test' => 'value'], true],
            [$class, [], false],
        ];
    }
}
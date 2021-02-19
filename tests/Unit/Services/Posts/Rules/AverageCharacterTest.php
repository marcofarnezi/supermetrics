<?php
namespace Unit\Services\Posts\Rules;

use App\Models\Interfaces\ModelInterface;
use App\Services\Interfaces\RuleInterface;
use App\Services\Post\Rules\AverageCharacter;
use PHPUnit\Framework\TestCase;
use Tests\Support\ReflectionTrait;
use DateTime;

class AverageCharacterTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var AverageCharacter
     */
    private $average_character;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->average_character = new AverageCharacter();
    }

    public function testIfAverageCharacterIsAInstanceOfRuleInterface()
    {
        $this->assertInstanceOf(RuleInterface::class, $this->average_character);
    }

    /**
     * @dataProvider isDataValidDataProvider
     */
    public function testIsDataValid(array $data, $output)
    {
        $is_data_valid_method = self::getAccessibleMethod(AverageCharacter::class, 'isDataValid');
        $result = $is_data_valid_method->invokeArgs($this->average_character, [$data]);
        $this->assertEquals($output, $result);
    }

    /**
     * @dataProvider runDataProvider
     */
    public function testRun(array $datas, array $output)
    {
        $average_character_mock = \Mockery::mock(AverageCharacter::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $average_character_mock->shouldReceive('isDataValid')
            ->andReturn(true);

        foreach ($datas as $data) {
            $model_mock = \Mockery::mock(ModelInterface::class)->makePartial();
            $model_mock->shouldReceive('getData')
                ->andReturn($data);
            $result = $average_character_mock->run($model_mock);

        }
        $this->assertEquals($output, $result);
    }

    /**
     * @dataProvider processResultDataProvider
     */
    public function testProcessResult(array $proccessed, array $output)
    {
        self::setValueInPrivateProperty(
            $this->average_character,
            AverageCharacter::class,
            'proccessed',
            $proccessed
        );

        $process_result_method = self::getAccessibleMethod(
            AverageCharacter::class,
            'processResult'
        );
        $this->assertEquals($output, $process_result_method->invoke($this->average_character));
    }

    public function testShowResult()
    {
        $data =  ['test'];
        $output = [
            'rule' => AverageCharacter::RULE_DESCRIPTION,
            'result' => $data
        ];

        $average_character_mock = \Mockery::mock(AverageCharacter::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $average_character_mock->shouldReceive('processResult')
            ->andReturn($data);

        $this->assertEquals($output, $average_character_mock->showResult());
    }

    public function isDataValidDataProvider(): array
    {
        $date = new DateTime();
        return [
            [
                [
                    'created_time' => $date,
                    'message' => 'test'
                ],
                true
            ],
            [
                [
                    'created_time' => $date
                ],
                false
            ],
            [
                [
                    'message' => 'test'
                ],
                false
            ],
            [
                [
                    'created_time' => '2000-01-01',
                    'message' => 'test'
                ],
                false
            ],
        ];
    }

    public function runDataProvider(): array
    {
        $date = new DateTime('2000-02-02');

        return [
            [
                [
                    [
                        'created_time' => $date,
                        'message' => 'test'
                    ],
                ],
                [
                    '2000-02' => [4]
                ]
            ]
        ];
    }

    public function processResultDataProvider(): array
    {
        return  [
            [
                [
                    '2000-02' => [4]
                ],
                [
                    '2000-02' => [
                        'total_post' => 1,
                        'average_length' => 4
                    ]
                ]
            ]
        ];
    }
}
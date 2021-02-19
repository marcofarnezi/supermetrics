<?php
namespace Unit\Services\Posts\Rules;

use App\Models\Interfaces\ModelInterface;
use App\Services\Interfaces\RuleInterface;
use App\Services\Post\Rules\LongestCharacter;
use DateTime;
use PHPUnit\Framework\TestCase;
use Tests\Support\ReflectionTrait;

class LongestCharacterTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var LongestCharacter
     */
    private $longest_character;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->longest_character = new LongestCharacter();
    }

    public function testIfLongestCharacterIsAInstanceOfRuleInterface()
    {
        $this->assertInstanceOf(RuleInterface::class, $this->longest_character);
    }

    /**
     * @dataProvider isDataValidDataProvider
     */
    public function testIsDataValid(array $data, $output)
    {
        $is_data_valid_method = self::getAccessibleMethod(
            LongestCharacter::class,
            'isDataValid'
        );
        $result = $is_data_valid_method->invokeArgs($this->longest_character, [$data]);
        $this->assertEquals($output, $result);
    }

    /**
     * @dataProvider runDataProvider
     */
    public function testRun(array $datas, array $output)
    {
        $longest_character_mock = \Mockery::mock(LongestCharacter::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $longest_character_mock->shouldReceive('isDataValid')
            ->andReturn(true);
        foreach ($datas as $data) {
            $model_mock = \Mockery::mock(ModelInterface::class)->makePartial();
            $model_mock->shouldReceive('getData')
                ->andReturn($data);
            $result = $longest_character_mock->run($model_mock);
        }
        $this->assertEquals($output, $result);
    }

    /**
     * @dataProvider processResultDataProvider
     */
    public function testProcessResult(array $proccessed, array $output)
    {
        self::setValueInPrivateProperty(
            $this->longest_character,
            LongestCharacter::class,
            'proccessed',
            $proccessed
        );

        $process_result_method = self::getAccessibleMethod(
            LongestCharacter::class,
            'processResult'
        );
        $this->assertEquals($output, $process_result_method->invoke($this->longest_character));
    }

    public function testShowResult()
    {
        $data =  ['test'];
        $output = [
            'rule' => LongestCharacter::RULE_DESCRIPTION,
            'result' => $data
        ];

        $longest_character = \Mockery::mock(LongestCharacter::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $longest_character->shouldReceive('processResult')
            ->andReturn($data);

        $this->assertEquals($output, $longest_character->showResult());
    }

    public function isDataValidDataProvider(): array
    {
        $date = new DateTime();
        return [
            [
                [
                    'created_time' => $date,
                    'id' => 1,
                    'message' => 'test'
                ],
                true
            ],
            [
                [
                    'id' => 1,
                    'created_time' => $date
                ],
                false
            ],
            [
                [
                    'id' => 1,
                    'message' => 'test'
                ],
                false
            ],
            [
                [
                    'created_time' => '2000-01-01',
                    'id' => 1,
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
                        'id' => 1,
                        'message' => 'test'
                    ],
                ],
                [
                    '2000-02' => [
                        'post' => 1,
                        'length' => 4
                    ]
                ]
            ]
        ];
    }

    public function processResultDataProvider(): array
    {
        return  [
            [
                [
                    [
                        '2000-02' => [
                            'post' => 1,
                            'length' => 4
                        ]
                    ]
                ],
                [
                    [
                        '2000-02' => [
                            'post' => 1,
                            'length' => 4
                        ]
                    ]
                ]
            ]
        ];
    }
}
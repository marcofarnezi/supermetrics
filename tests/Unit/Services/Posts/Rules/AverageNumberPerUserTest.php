<?php
namespace Unit\Services\Posts\Rules;

use App\Models\Interfaces\ModelInterface;
use App\Services\Interfaces\RuleInterface;
use App\Services\Post\Rules\AverageNumberPerUser;
use PHPUnit\Framework\TestCase;
use Tests\Support\ReflectionTrait;
use DateTime;

class AverageNumberPerUserTest extends TestCase
{
    use ReflectionTrait;
    /**
     * @var AverageNumberPerUser
     */
    private $average_number_per_user;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->average_number_per_user = new AverageNumberPerUser();
    }

    public function testIfAverageNumberPerUserIsAInstanceOfRuleInterface()
    {
        $this->assertInstanceOf(RuleInterface::class, $this->average_number_per_user);
    }

    /**
     * @dataProvider isDataValidDataProvider
     */
    public function testIsDataValid(array $data, $output)
    {
        $is_data_valid_method = self::getAccessibleMethod(
            AverageNumberPerUser::class,
            'isDataValid'
        );
        $result = $is_data_valid_method->invokeArgs($this->average_number_per_user, [$data]);
        $this->assertEquals($output, $result);
    }

    /**
     * @dataProvider runDataProvider
     */
    public function testRun(array $datas, array $output)
    {
        $average_number_per_user_mock = \Mockery::mock(AverageNumberPerUser::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $average_number_per_user_mock->shouldReceive('isDataValid')
            ->andReturn(true);

        foreach ($datas as $data) {
            $model_mock = \Mockery::mock(ModelInterface::class)->makePartial();
            $model_mock->shouldReceive('getData')
                ->andReturn($data);
            $result = $average_number_per_user_mock->run($model_mock);
        }
        $this->assertEquals($output, $result);
    }

    /**
     * @dataProvider processResultDataProvider
     */
    public function testProcessResult(array $proccessed, array $output)
    {
        self::setValueInPrivateProperty(
            $this->average_number_per_user,
            AverageNumberPerUser::class,
            'proccessed',
            $proccessed
        );

        $process_result_method = self::getAccessibleMethod(
            AverageNumberPerUser::class,
            'processResult'
        );
        $this->assertEquals($output, $process_result_method->invoke($this->average_number_per_user));
    }

    public function testShowResult()
    {
        $data =  ['test'];
        $output = [
            'rule' => AverageNumberPerUser::RULE_DESCRIPTION,
            'result' => $data
        ];

        $average_number_per_user = \Mockery::mock(AverageNumberPerUser::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $average_number_per_user->shouldReceive('processResult')
            ->andReturn($data);

        $this->assertEquals($output, $average_number_per_user->showResult());
    }

    public function isDataValidDataProvider(): array
    {
        $user_identify = AverageNumberPerUser::FROM_USER;
        $date = new DateTime();
        return [
            [
                [
                    'created_time' => $date,
                    $user_identify => 'test'
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
                    $user_identify => 'test'
                ],
                false
            ],
            [
                [
                    'created_time' => '2000-01-01',
                    $user_identify => 'test'
                ],
                false
            ],
        ];
    }

    public function runDataProvider(): array
    {
        $user_identify = AverageNumberPerUser::FROM_USER;
        $date = new DateTime('2000-02-02');
        return [
            [
                [
                    [
                        'created_time' => $date,
                        $user_identify => 'test'
                    ],
                ],
                [
                    '2000-02' => [
                        'test' =>  [
                            [
                                'created_time' => $date,
                                $user_identify => 'test'
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function processResultDataProvider(): array
    {
        $user_identify = AverageNumberPerUser::FROM_USER;
        $date = new DateTime('2000-02-02');
        return  [
            [
                [
                    '2000-02' => [
                        'test' =>  [
                            [
                                'created_time' => $date,
                                $user_identify => 'test'
                            ]
                        ]
                    ]
                ],
                [
                    '2000-02' => [
                        'test' => [
                            'total_post' => 1
                        ]
                    ]
                ]
            ]
        ];
    }
}
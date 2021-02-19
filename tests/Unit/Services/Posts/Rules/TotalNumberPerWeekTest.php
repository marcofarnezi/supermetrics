<?php
namespace Unit\Services\Posts\Rules;

use App\Models\Interfaces\ModelInterface;
use App\Services\Interfaces\RuleInterface;
use App\Services\Post\Rules\TotalNumberPerWeek;
use PHPUnit\Framework\TestCase;
use Tests\Support\ReflectionTrait;
use DateTime;

class TotalNumberPerWeekTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var TotalNumberPerWeek
     */
    private $total_numver_per_week;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->total_numver_per_week = new TotalNumberPerWeek();
    }

    public function testIfTotalNumberPerWeekIsAInstanceOfRuleInterface()
    {
        $this->assertInstanceOf(RuleInterface::class, $this->total_numver_per_week);
    }

    /**
     * @dataProvider isDataValidDataProvider
     */
    public function testIsDataValid(array $data, $output)
    {
        $is_data_valid_method = self::getAccessibleMethod(
            TotalNumberPerWeek::class,
            'isDataValid'
        );
        $result = $is_data_valid_method->invokeArgs($this->total_numver_per_week, [$data]);
        $this->assertEquals($output, $result);
    }

    /**
     * @dataProvider runDataProvider
     */
    public function testRun(array $datas, array $output)
    {
        $total_number_per_week_mock = \Mockery::mock(TotalNumberPerWeek::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $total_number_per_week_mock->shouldReceive('isDataValid')
            ->andReturn(true);

        foreach ($datas as $data) {
            $model_mock = \Mockery::mock(ModelInterface::class)->makePartial();
            $model_mock->shouldReceive('getData')
                ->andReturn($data);
            $result = $total_number_per_week_mock->run($model_mock);
        }
        $this->assertEquals($output, $result);
    }

    /**
     * @dataProvider processResultDataProvider
     */
    public function testProcessResult(array $proccessed, array $output)
    {
        self::setValueInPrivateProperty(
            $this->total_numver_per_week,
            TotalNumberPerWeek::class,
            'proccessed',
            $proccessed
        );

        $process_result_method = self::getAccessibleMethod(
            TotalNumberPerWeek::class,
            'processResult'
        );

        $this->assertEquals($output, $process_result_method->invoke($this->total_numver_per_week));
    }

    public function testShowResult()
    {
        $data =  ['test'];
        $output = [
            'rule' => TotalNumberPerWeek::RULE_DESCRIPTION,
            'result' => $data
        ];

        $total_numver_per_week = \Mockery::mock(TotalNumberPerWeek::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $total_numver_per_week->shouldReceive('processResult')
            ->andReturn($data);

        $this->assertEquals($output, $total_numver_per_week->showResult());
    }

    public function isDataValidDataProvider(): array
    {
        $date = new DateTime();
        return [
            [
                [
                    'created_time' => $date,
                ],
                true
            ],
            [
                [],
                false
            ],
            [
                [
                    'created_time' => '2000-01-01',
                ],
                false
            ],
        ];
    }

    public function runDataProvider(): array
    {
        $date = new DateTime('2000-02-02');
        $date2 = new DateTime('2000-03-02');
        return [
            [
                [
                    [
                        'created_time' => $date,
                    ],
                ],
                [
                    '6' => ['num_posts' => 1]
                ]
            ],
            [
                [
                    [
                        'created_time' => $date,
                    ],
                    [
                        'created_time' => $date,
                    ],
                ],
                [
                    '6' => ['num_posts' => 2]
                ]
            ],
            [
                [
                    [
                        'created_time' => $date,
                    ],
                    [
                        'created_time' => $date2,
                    ],
                ],
                [
                    '6' => ['num_posts' => 1],
                    '10' => ['num_posts' => 1]
                ]
            ]

        ];
    }

    public function processResultDataProvider(): array
    {
        return  [
            [
                ['6' => ['num_posts' => 2]],
                ['6' => ['num_posts' => 2]]
            ],
        ];
    }
}
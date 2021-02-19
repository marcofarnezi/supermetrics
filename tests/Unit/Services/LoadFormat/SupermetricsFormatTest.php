<?php
namespace Unit\Services\LoadFormat;

use App\Services\Interfaces\LoadFormatInterface;
use App\Services\LoadFormat\SupermetricsFormat;
use PHPUnit\Framework\TestCase;

class SupermetricsFormatTest extends TestCase
{
    /**
     * @var SupermetricsFormat
     */
    private $supermetrics_format;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->supermetrics_format = new SupermetricsFormat();
    }

    public function testIfSupermetricsFormatIsAInstanceOfLoadFormatInterface()
    {
        $this->assertInstanceOf(LoadFormatInterface::class, $this->supermetrics_format);
    }

    /**
     * @dataProvider supermetricsGetReturns
     */
    public function testReturnFromSupermetricsFormatGet(array $input, array $output)
    {
        $this->assertEquals($output, $this->supermetrics_format->get($input));
    }

    public function supermetricsGetReturns(): array
    {
        return [
            [
                ['data' => ['test']], ['test']
            ],
            [
                ['datas' => ['test']], []
            ],
            [
                ['test'], []
            ],
        ];
    }
}
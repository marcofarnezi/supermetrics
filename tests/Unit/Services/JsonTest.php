<?php
namespace Tests\Unit\Services;

use App\Services\Interfaces\DisplayInterface;
use App\Services\Json;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    public function testIfJsonIsAInstanceOfClientInterface()
    {
        $json = new Json();
        $this->assertInstanceOf(DisplayInterface::class, $json);
    }

    public function testDisplayJson()
    {
        $example = ['test' => 'display'];
        $output = '{"test":"display"}';
        $this->expectOutputString($output);
        Json::display($example);
    }
}
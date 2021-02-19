<?php
namespace Tests\Unit\Services;

use App\Services\Client;
use App\Services\Interfaces\ClientInterface;
use PHPUnit\Framework\TestCase;
use Tests\Support\ReflectionTrait;

class ClientTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var Client
     */
    private $client;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->client = new Client();
    }

    public function testIfClientIsAInstanceOfClientInterface()
    {
        $this->assertInstanceOf(ClientInterface::class, $this->client);
    }

    public function testSetUrl()
    {
        $url_expected = 'http://test';
        $return = $this->client->setUrl($url_expected);
        $url_property = self::setPropertyAccessiblePublic(Client::class, 'url');
        $this->assertEquals($url_expected, $url_property->getValue($this->client));
        $this->assertInstanceOf(Client::class, $return);
    }

    public function testGetUrl()
    {
        $url_expected = 'http://test';
        $this->client->setUrl($url_expected);
        $this->assertEquals($url_expected, $this->client->getUrl());
    }

    public function testSetMethod()
    {
        $method = 'get';
        $method_expected = 'GET';
        $return = $this->client->setMethod($method);
        $method_property = self::setPropertyAccessiblePublic(Client::class, 'method');
        $this->assertEquals($method_expected, $method_property->getValue($this->client));
        $this->assertInstanceOf(Client::class, $return);
    }

    public function testGetMethod()
    {
        $method = 'get';
        $method_expected = 'GET';
        $this->client->setMethod($method);
        $this->assertEquals($method_expected, $this->client->getMethod());
    }

    public function tesIsMethodPost()
    {
        $method = 'get';
        $this->client->setMethod($method);
        $this->assertNotTrue($this->client->isMethodPost());

        $method = 'post';
        $this->client->setMethod($method);
        $this->assertTrue($this->client->isMethodPost());

        $method = 'PoSt';
        $this->client->setMethod($method);
        $this->assertTrue($this->client->isMethodPost());
    }

    public function testSetParameters()
    {
        $param_expected = ['test' => 'param'];
        $return = $this->client->setParameters($param_expected);
        $parameters_property = self::setPropertyAccessiblePublic(Client::class, 'parameters');
        $this->assertEquals($param_expected, $parameters_property->getValue($this->client));
        $this->assertInstanceOf(Client::class, $return);
    }

    public function testGetParameters()
    {
        $param_expected = ['test' => 'param'];
        $this->client->setParameters($param_expected);
        $this->assertEquals($param_expected, $this->client->getParameters());
    }
}
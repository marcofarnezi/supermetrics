<?php
namespace App\Services;

use App\Services\Interfaces\ClientInterface;

class Client implements ClientInterface
{
    private $url;
    private $method;
    private $parameters;

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    public function __construct()
    {
        $this->parameters = [];
        $this->method = self::METHOD_GET;
    }

    public function setUrl(string $url): ClientInterface
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setMethod(string $method): ClientInterface
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function isMethodPost(): bool
    {
        return $this->method === self::METHOD_POST;
    }

    public function setParameters(array $parameters): ClientInterface
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
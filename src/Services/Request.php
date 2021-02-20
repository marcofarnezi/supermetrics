<?php
namespace App\Services;

use App\Services\Interfaces\ClientInterface;
use App\Services\Interfaces\RequestInterface;
use App\Services\Traits\FormatResponse;

class Request implements RequestInterface
{
    use FormatResponse;

    const SUCCESS_STATUS = 200;

    private $client;
    private $resource;
    private $outPut;

    public function setClient(ClientInterface $client): RequestInterface
    {
        $this->client = $client;
        $resource = $this->getNewResource($client->getUrl());
        $resource = $this->setMethod($resource, $client->getMethod());
        $this->resource = $this->setPayload($resource, $client);
        return $this;
    }

    protected function executeRequest($resource): string
    {
        return curl_exec($resource);
    }

    public function send(): array
    {
        $this->outPut = $this->executeRequest($this->resource);
        return $this->getResponse();

    }

    protected function setOptions($resource, array $option): void
    {
        curl_setopt_array($resource, $option);
    }

    protected function setMethod($resource, string $method)
    {
        $this->setOptions($resource, [CURLOPT_CUSTOMREQUEST => $method]);
        return $resource;
    }

    protected function prepareRequest($resource)
    {
        $this->setOptions($resource, [
            CURLOPT_HEADER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 300
        ]);
        return $resource;
    }

    protected function getResource(string $url)
    {
        return curl_init($url);
    }

    protected function getNewResource(string $url)
    {
        return $this->prepareRequest($this->getResource($url));
    }

    protected function setPayload($resource, ClientInterface $client)
    {
        $this->setOptions(
            $resource,
            [
                CURLOPT_POST => $client->isMethodPost(),
                CURLOPT_POSTFIELDS => $client->getParameters()
            ]
        );
        return $resource;
    }

    public function isSuccess(int $status): bool
    {
        return self::SUCCESS_STATUS == $status;
    }

    protected function getHeaderLength($resource): int
    {
        return curl_getinfo($resource, CURLINFO_HEADER_SIZE);
    }

    protected function getStatus($resource): int
    {
        return curl_getinfo($resource, CURLINFO_HTTP_CODE);
    }

    protected function closeResource($resource): void
    {
        curl_close($resource);
    }

    protected function getResponse(): array
    {
        $header_length = $this->getHeaderLength($this->resource);
        $status = $this->getStatus($this->resource);
        $this->closeResource($this->resource);
        return [
            $this->getCompleteResponse($status, $header_length, $this->outPut, $this->isSuccess($status))
        ];
    }

}
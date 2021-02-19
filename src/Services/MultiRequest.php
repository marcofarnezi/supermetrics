<?php
namespace App\Services;

use App\Services\Interfaces\ClientInterface;
use App\Services\Interfaces\RequestInterface;

class MultiRequest extends Request
{
    private $multi_resource;
    private $resources;

    public function __construct()
    {
        $this->multi_resource = curl_multi_init();
        $this->resources = [];
    }

    protected function setResource($resource): void
    {
        curl_multi_add_handle($this->multi_resource, $resource);
    }

    public function setClient(ClientInterface $client): RequestInterface
    {
        $resource = $this->getNewResource($client->getUrl());
        $resource= $this->setPayload($resource, $client);
        $resource= $this->setMethod($resource, $client->getMethod());
        $this->setResource($resource);
        $this->resources[] = $resource;
        return $this;
    }

    public function send(): array
    {
        return $this->processAll()->getResponse();
    }

    protected function processAll(): self
    {
        do {
            $status = curl_multi_exec($this->multi_resource, $active);
            if ($active) {
                curl_multi_select($this->multi_resource);
            }
        } while ($active && $status == CURLM_OK);

        return $this;
    }

    protected function executeRequest($resource): string
    {
        return curl_multi_getcontent($resource);
    }

    protected function closeResource($resource): void
    {
        curl_multi_remove_handle($this->multi_resource, $resource);
    }

    protected function closeMultiResource(): void
    {
        curl_multi_close($this->multi_resource);
    }

    protected function getResponse(): array
    {
        $out_puts = [];
        foreach ($this->resources as $resource) {
            $out_put = $this->executeRequest($resource);
            $header_length = $this->getHeaderLength($resource);
            $status = $this->getStatus($resource);
            $out_puts[] = $this->getCompleteResponse($status, $header_length, $out_put, $this->isSuccess($status));
            $this->closeResource($resource);
        }
        $this->closeMultiResource();
        return $out_puts;
    }

}
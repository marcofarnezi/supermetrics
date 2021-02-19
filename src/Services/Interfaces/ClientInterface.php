<?php
namespace App\Services\Interfaces;

interface ClientInterface
{
    public function setUrl(string $url): ClientInterface;
    public function getUrl(): string;
    public function setMethod(string $method): ClientInterface;
    public function getMethod(): string;
    public function isMethodPost(): bool;
    public function setParameters(array $parameters): ClientInterface;
    public function getParameters(): array;
}
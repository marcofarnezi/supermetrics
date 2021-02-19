<?php
namespace App\Services\Interfaces;

interface RequestInterface
{
    public function setClient(ClientInterface $client): RequestInterface;
    public function send(): array;
    public function isSuccess(int $status): bool;
}
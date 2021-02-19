<?php
namespace App\Services\Interfaces;

interface LoaderInterface
{
    public function setToLoad(RequestInterface $request, string $model_name): LoaderInterface;
    public function load(LoadFormatInterface $load_format): array;
}
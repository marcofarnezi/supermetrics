<?php
namespace App\Services\Traits;

trait FormatResponse
{
    /**
     * @var int
     */
    protected $header_length;

    public function setHeaderLength(int $header_length)
    {
        $this->header_length = $header_length;
    }

    public function getHeader(string $out_put): string
    {
        return substr($out_put, 0, $this->header_length);
    }

    public function getBody(string $out_put): string
    {
        return substr($out_put, $this->header_length);
    }

    public function getCompleteResponse(
        int $status,
        int $header_length,
        string $out_put,
        bool $convert_json = true
    ): array
    {
        $this->setHeaderLength($header_length);
        return [
            "status" =>  $status,
            "header" =>  $this->getHeader($out_put),
            "body" =>  $convert_json
                ? json_decode($this->getBody($out_put), true)
                : $this->getBody($out_put)
        ];
    }
}
<?php
namespace App\Controllers;

use App\Models\LoginToken;
use App\Models\Posts;
use App\Services\Client;
use App\Services\Interfaces\CalculatorInterface;
use App\Services\Interfaces\ClientInterface;
use App\Services\Interfaces\LoaderInterface;
use App\Services\Interfaces\LoadFormatInterface;
use App\Services\Interfaces\RequestInterface;
use Predis\Client as RedisClient;

class SupermetricsController
{
    const TOKEN_NAME = 'SupermetricToken';
    const EXPIRE_RESOLUTION = 'EX';
    const REDIS_TIMEOUT_ONE_HOUR = 60 * 60;
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var LoadFormatInterface
     */
    private $loadFormat;

    /**
     * @var RedisClient
     */
    private $redis;

    public function __construct(LoaderInterface $loader, LoadFormatInterface $loadFormat, RedisClient $redis)
    {
        $this->loader = $loader;
        $this->loadFormat = $loadFormat;
        $this->redis = $redis;
    }

    public function token(ClientInterface $client, RequestInterface $request): string
    {
        $token = $this->redis->get(self::TOKEN_NAME);
        if (! empty($token)) {
            return $token;
        }
        $client->setUrl($_ENV['SUPERMETRICS_API_URL'] . 'register')
            ->setMethod(Client::METHOD_POST)
            ->setParameters(
                [
                    'client_id' => $_ENV['SUPERMETRICS_API_CLIENT_ID'],
                    'email' => $_ENV['SUPERMETRICS_API_EMAIL'],
                    'name' => $_ENV['SUPERMETRICS_API_NAME']
                ]
            );
        $request->setClient($client);
        $loginTokenList = $this->loader->setToLoad($request, LoginToken::class)
            ->load($this->loadFormat);
        $data = end($loginTokenList)->getData();
        $this->redis->set(
            self::TOKEN_NAME,
            $data['sl_token'],
            self::EXPIRE_RESOLUTION,
            self::REDIS_TIMEOUT_ONE_HOUR
        );

        return $data['sl_token'] ?? '';
    }

    public function posts(
        ClientInterface $client,
        RequestInterface $request,
        $token,
        int $page_end = 1
    ): array
    {
        for ($page = 1; $page <= $page_end; $page++) {;
            $client_clone = clone $client;
            $client_clone->setUrl($_ENV['SUPERMETRICS_API_URL'] .'posts?sl_token=' . $token . '&page=' . $page);
            $request->setClient($client_clone);
        }

        return $this->loader->setToLoad($request, Posts::class)
            ->load($this->loadFormat);
    }

    public function summary(CalculatorInterface $calculator, array $posts_list): array
    {
        /**
         * @var Posts $posts
         */
        foreach ($posts_list as $posts) {
            foreach ($posts->getData() as $post) {
                $calculator->setModel($post);
            }
        }

        return $calculator->calculate();
    }
}
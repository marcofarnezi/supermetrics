<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\SupermetricsController;
use App\Services\Client;
use App\Services\LoaderModule;
use App\Services\LoadFormat\SupermetricsFormat;
use App\Services\MultiRequest;
use App\Services\Post\Rules\AverageCharacter;
use App\Services\Post\Rules\AverageNumberPerUser;
use App\Services\Post\Rules\LongestCharacter;
use App\Services\Post\Rules\TotalNumberPerWeek;
use App\Services\Post\PostsCalculator;
use App\Services\Request;
use App\Services\Json;
use Predis\Client as RedisClient;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$calculator = new PostsCalculator();
$calculator->setRule(new AverageCharacter())
    ->setRule(new AverageNumberPerUser())
    ->setRule(new LongestCharacter())
    ->setRule(new TotalNumberPerWeek());
$redis = new RedisClient($_ENV['REDIS_URL']);
$supermetrics_controller = new SupermetricsController(new LoaderModule(), new SupermetricsFormat(), $redis);
$token = $supermetrics_controller->token(new Client(), new Request());
$posts_list = $supermetrics_controller->posts(new Client(), new MultiRequest(), 10, $token);

Json::display($supermetrics_controller->summary($calculator, $posts_list));
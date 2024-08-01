<?php

declare(strict_types=1);
/**
 * This file is part of hyperf-ext/elasticsearch.
 *
 * @link     https://github.com/hyperf-ext/elasticsearch
 * @contact  eric@zhu.email
 * @license  https://github.com/hyperf-ext/elasticsearch/blob/master/LICENSE
 */
namespace HyperfExt\Elasticsearch;

use Elastic\Elasticsearch\ClientBuilder;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Guzzle\RingPHP\CoroutineHandler;
use Hyperf\Guzzle\RingPHP\PoolHandler;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Swoole\Coroutine;
use GuzzleHttp\Client;
use function Hyperf\Support\make;

class ClientFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class)->get('elasticsearch');

        $clientConfig = $config['client'];
        $loggerConfig = $config['logger'];

        if (! isset($clientConfig['httpClient']) and Coroutine::getCid() > 0) {

            $clientConfig['httpClient'] = new Client();
        }

        if (! isset($clientConfig['logger']) and $loggerConfig['enabled']) {
            $logger = $container->get(LoggerFactory::class)->get($loggerConfig['name'], $loggerConfig['group']);
            $clientConfig['logger'] = $logger;
        }

        return ClientBuilder::fromConfig($clientConfig);
    }
}

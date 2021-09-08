<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp;

use Losingbattle\RocketMqHttp\Contract\ConsumerLoggerInterface;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Guzzle\ClientFactory as GuzzleClientFactory;
use Psr\Container\ContainerInterface;

class ConsumerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);

        /**
         * @var Option
         */
        $option = new Option();
        $host = $config->get('rocketmq-http.host');
        $option->setAccessKeyId($config->get('rocketmq-http.access_key_id'))
            ->setAccessKeySecret($config->get('rocketmq-http.access_key_secret'))
            ->setInstanceId($config->get('rocketmq-http.instance_id'));

        $httpClientFactory = function () use ($container,$host) {
            return $container->get(GuzzleClientFactory::class)->create([
                'base_uri' => $host,
            ]);
        };

        if ($container->has(ConsumerLoggerInterface::class)) {
            $logger = $container->get(ConsumerLoggerInterface::class);
        } else {
            $logger = null;
        }

        return new Consumer($container,$httpClientFactory, $option, $logger);
    }
}

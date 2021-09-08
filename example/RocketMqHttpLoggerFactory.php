<?php

declare(strict_types=1);

use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;

class RocketMqHttpLoggerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return $container->get(LoggerFactory::class)->get('rocketmq-http', 'rocketmq-http');
    }
}

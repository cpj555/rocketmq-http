<?php

declare(strict_types=1);


namespace Losingbattle\RocketMqHttp;

use Losingbattle\RocketMqHttp\Contract\PackerInterface;
use Losingbattle\RocketMqHttp\Listener\BeforeMainServerStartListener;
use Hyperf\Utils\Packer\JsonPacker;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                PackerInterface::class => JsonPacker::class,
                Producer::class => ProducerFactory::class,
                Consumer::class => ConsumerFactory::class,
            ],
            'processes' => [
            ],
            'listeners' => [
                BeforeMainServerStartListener::class => 99,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for rocketmq-http.',
                    'source' => __DIR__ . '/../publish/rocketmq-http.php',
                    'destination' => BASE_PATH . '/config/autoload/rocketmq-http.php',
                ],
            ],
        ];
    }
}

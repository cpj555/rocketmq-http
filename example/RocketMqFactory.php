<?php

use Losingbattle\RocketMqHttp\Consumer;
use Losingbattle\RocketMqHttp\Contract\PackerInterface;
use Losingbattle\RocketMqHttp\Option;
use Losingbattle\RocketMqHttp\Producer;
use GuzzleHttp\Client;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Packer\JsonPacker;
use Pimple\Container;

class RocketMqFactory
{
    public function getProducer()
    {
        $config = [];

        $option = new Option();

        $host = $config['host'];
        $option->setAccessKeyId($config['access_key_id']);
        $option->setAccessKeySecret($config['access_key_secret']);
        $option->setInstanceId($config['instance_id']);

        ApplicationContext::setContainer(new \Pimple\Psr11\Container(new Container([PackerInterface::class => new JsonPacker()])));

        $httpClientFactory = function () use ($host) {
            return new Client([
                'base_uri' => $host,
            ]);
        };

        $logger = null;

        return new Producer($httpClientFactory, $option, $logger);
    }

    public function getConsumer()
    {
        $config = [];

        $option = new Option();

        $host = $config['host'];
        $option->setAccessKeyId($config['access_key_id']);
        $option->setAccessKeySecret($config['access_key_secret']);
        $option->setInstanceId($config['access_key_secret']);
        $option->setInstanceId($config['instance_id']);

        $container = ApplicationContext::setContainer(new \Pimple\Psr11\Container(new Container([
            ConfigInterface::class => new Config(),
            PackerInterface::class => new JsonPacker()
        ])));

        $httpClientFactory = function () use ($host) {
            return new Client([
                'base_uri' => $host,
            ]);
        };

        $logger = null;

        return new Consumer($container, $httpClientFactory, $option, $logger);
    }
}

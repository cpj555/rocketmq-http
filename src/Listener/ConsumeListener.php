<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Listener;

use Losingbattle\RocketMqHttp\Event\AfterConsume;
use Losingbattle\RocketMqHttp\Event\BeforeConsume;
use Losingbattle\RocketMqHttp\Event\ConsumeEvent;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;

class ConsumeListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(LoggerFactory::class)->get('rocketmq-http', 'rocketmq-http');
    }

    public function listen(): array
    {
        return [
            BeforeConsume::class,
            AfterConsume::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof ConsumeEvent) {
            switch (true) {
                case $event instanceof BeforeConsume:
                    $this->logger->info('beforeConsume', $event->getMessage()->toArray());
                    break;
                case $event instanceof AfterConsume:
                    $this->logger->info('afterConsume', [
                        'MessageId' => $event->getMessage()->getMessageId(),
                        'result' => $event->getResult(),
                    ]);
            }
        }
    }
}

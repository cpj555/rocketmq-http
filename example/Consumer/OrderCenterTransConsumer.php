<?php

declare(strict_types=1);

namespace Example\Consumer;

use Example\Consumer\MessageData\OrderCloseData;
use Example\Consumer\MessageData\OrderSubmitData;
use Losingbattle\RocketMqHttp\Annotation\Consumer;
use Losingbattle\RocketMqHttp\Message\ConsumerMessage;
use Losingbattle\RocketMqHttp\Result;

/**
 * @Consumer(groupId="GID_order_trans", topic="order_trans_topic", numOfMessages=16, waitSeconds=30)
 */
class OrderCenterTransConsumer extends ConsumerMessage
{
    public function __construct()
    {
        $this->registerRoute('order_create', [$this, 'orderCreate']);
    }

    public function isEnable(): bool
    {
        return false;
    }

    public function orderCreate(array $data): string
    {
        dd($data);
        return Result::ACK;
    }
}

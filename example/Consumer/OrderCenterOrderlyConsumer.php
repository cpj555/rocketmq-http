<?php

declare(strict_types=1);

namespace Example\Consumer;

use Example\Consumer\MessageData\OrderCloseData;
use Example\Consumer\MessageData\OrderSubmitData;
use Losingbattle\RocketMqHttp\Annotation\Consumer;
use Losingbattle\RocketMqHttp\Message\ConsumerMessage;
use Losingbattle\RocketMqHttp\Result;

/**
 * @Consumer(groupId="GID_order_orderly", topic="order_orderly_topic",orderly=true, numOfMessages=16, waitSeconds=30)
 */
class OrderCenterOrderlyConsumer extends ConsumerMessage
{
    public function __construct()
    {
        $this->registerRoute('order_status', [$this, 'orderStatus']);
    }

    public function isEnable(): bool
    {
        return false;
    }

    public function orderStatus(array $data)
    {

        var_dump($data);
        if($data['order_status'] == 1){
            return Result::NACK;
        }

//        dump($data);

        return Result::ACK;
    }
}

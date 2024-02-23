<?php

declare(strict_types=1);



use Example\Consumer\MessageData\OrderCloseData;
use Example\Consumer\MessageData\OrderSubmitData;
use Losingbattle\RocketMqHttp\Annotation\Consumer;
use Losingbattle\RocketMqHttp\Message\ConsumerMessage;
use Losingbattle\RocketMqHttp\Result;


#[Consumer(topic: "order_orderly_topic", groupId: "GID_order_orderly", numOfMessages: 16, waitSeconds: 30, orderly: true)]
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

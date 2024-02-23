<?php

declare(strict_types=1);



use Losingbattle\RocketMqHttp\Annotation\Producer;
use Losingbattle\RocketMqHttp\Message\ProducerMessage;


#[Producer(topic: "order_normal_topic", tag: "order_close",delayTtl: 10)]
class OrderCloseDelayMessage extends ProducerMessage
{

    public function setOrderNo(string $orderNo): OrderCloseDelayMessage
    {
        return $this->setMessageBody('order_no', $orderNo);
    }
}

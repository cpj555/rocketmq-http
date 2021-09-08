<?php

declare(strict_types=1);



use Losingbattle\RocketMqHttp\Annotation\Producer;
use Losingbattle\RocketMqHttp\Message\ProducerMessage;

/**
 * @Producer(topic="order-n-message", tag="order_submit")
 */
class OrderSubmitNormalMessage extends ProducerMessage
{
    public function setOrderNo(string $orderNo): OrderSubmitNormalMessage
    {
        return $this->setMessageBody('order_no', $orderNo);
    }
}

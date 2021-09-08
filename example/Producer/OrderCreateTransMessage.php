<?php

declare(strict_types=1);


namespace Example\Producer;

use Losingbattle\RocketMqHttp\Annotation\Producer;
use Losingbattle\RocketMqHttp\Message\ProducerMessage;

/**
 * @Producer(topic="order_trans_topic",groupId="GID_order_trans", tag="order_create", transctionCheckTtl=10)
 */
class OrderCreateTransMessage extends ProducerMessage
{
    public function setOrderNo(string $orderNo): OrderCreateTransMessage
    {
        return $this->setMessageBody('order_no', $orderNo);
    }
}

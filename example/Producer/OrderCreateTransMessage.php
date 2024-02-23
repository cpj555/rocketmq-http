<?php

declare(strict_types=1);




use Losingbattle\RocketMqHttp\Annotation\Producer;
use Losingbattle\RocketMqHttp\Message\ProducerMessage;


#[Producer(groupId: "GID_order_trans", topic: "order_trans_topic", tag: "order_close", transctionCheckTtl: 10)]
class OrderCreateTransMessage extends ProducerMessage
{
    public function setOrderNo(string $orderNo): OrderCreateTransMessage
    {
        return $this->setMessageBody('order_no', $orderNo);
    }
}

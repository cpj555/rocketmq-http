<?php
/**
 * User: cpj
 * Date: 2021/1/20
 */




use Losingbattle\RocketMqHttp\Annotation\Producer;
use Losingbattle\RocketMqHttp\Message\ProducerMessage;

/**
 * @Producer(topic="order_orderly_topic", tag="order_status")
 */
class OrderStatusOrderlyMessage extends ProducerMessage
{
    public function setOrderNo(string $orderNo): OrderStatusOrderlyMessage
    {
        $this->setShardingKey($orderNo);
        return $this->setMessageBody('order_no', $orderNo);
    }

    public function setOrderStatus(int $orderStatus): OrderStatusOrderlyMessage
    {
        return $this->setMessageBody('order_status',$orderStatus);
    }
}
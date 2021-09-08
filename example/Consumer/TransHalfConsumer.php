<?php

declare(strict_types=1);




use Losingbattle\RocketMqHttp\Annotation\Consumer;
use Losingbattle\RocketMqHttp\Message\ConsumerMessage;

/**
 * @Consumer(groupId="GID_trans_half_consumer", topic="order_trans_topic",halfTrans=true)
 */
class TransHalfConsumer extends ConsumerMessage
{
    public function __construct()
    {
        $this->registerRoute('order_submit', [$this, 'orderSubmit']);
    }

    public function isEnable(): bool
    {
        return false;
    }

    public function orderSubmit(array $data)
    {

        dd("halfTrans");
        dd($data);
    }
}

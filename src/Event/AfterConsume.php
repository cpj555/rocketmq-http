<?php

declare(strict_types=1);


namespace Losingbattle\RocketMqHttp\Event;

use Losingbattle\RocketMqHttp\Message\Response\ConsumeMessageResponse;

class AfterConsume extends ConsumeEvent
{
    protected $result;

    public function __construct(ConsumeMessageResponse $message, string $result)
    {
        parent::__construct($message);
        $this->result = $result;
    }

    public function getResult(): string
    {
        return $this->result;
    }
}

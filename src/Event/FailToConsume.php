<?php

declare(strict_types=1);


namespace Losingbattle\RocketMqHttp\Event;

use Losingbattle\RocketMqHttp\Message\Response\ConsumeMessageResponse;
use Throwable;

class FailToConsume extends ConsumeEvent
{
    /**
     * @var Throwable
     */
    protected $throwable;

    public function __construct(ConsumeMessageResponse $message, Throwable $throwable)
    {
        parent::__construct($message);
        $this->throwable = $throwable;
    }

    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }
}

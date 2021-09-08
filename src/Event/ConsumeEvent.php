<?php

declare(strict_types=1);

namespace Losingbattle\RocketMqHttp\Event;

use Losingbattle\RocketMqHttp\Message\Response\ConsumeMessageResponse;

class ConsumeEvent
{
    /**
     * @var ConsumeMessageResponse
     */
    protected $message;

    public function __construct(ConsumeMessageResponse $message)
    {
        $this->message = $message;
    }

    public function getMessage(): ConsumeMessageResponse
    {
        return  $this->message;
    }
}
